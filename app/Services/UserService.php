<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\Helper;
use App\Jobs\VerifyUserMail;
use App\Mail\UserApprovedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\User\Resource;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\Facades\MediaUploader;

class UserService
{
    private User $userObj;

    private UserOtpService $userOtpService;

    public function __construct()
    {
        $this->userObj = new User;

        $this->userOtpService = new UserOtpService;
    }

    public function resource(int $id, array $inputs = []): User
    {
        $user = $this->userObj->getQB()->findOrFail($id);

        return $user;
    }

    public function update(int $id, array $inputs = []): array
    {
        $user = Auth::user();

        /**
         * Currently we are not update the users email 
         * only update the name and phone number and profile image
         */

        // if (!empty($inputs['email']) && $inputs['email'] != $user->email) {
        //     $inputs['email_verified_at'] = null;
        //     $otp = Helper::generateOTP(config('site.generate_otp_length'));
        //     $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'verification']);

        //     try {
        //         VerifyUserMail::dispatch($user, $otp);
        //     } catch (\Exception $e) {
        //         Log::info('User verification mail failed.' . $e->getMessage());
        //     }

        //     $user->update($inputs);
        //     $data = [
        //         'message' => __('message.updateUserVerifySuccess'),
        //         'user' => new Resource($user),
        //     ];
        // } else {

        // Handle profile image upload if present
        if (request()->hasFile('profile_image')) {
            // Get old media
            $oldMedia = $user->getMedia('profile')->first();

            // Upload new media
            $media = MediaUploader::fromSource(request()->file('profile_image'))
                ->toDisk('public')
                ->toDirectory('users/profile')
                ->upload();

            // Associate the new media
            $user->syncMedia($media, 'profile');

            // Delete old media file from disk and DB
            if ($oldMedia && $media) {
                $oldMedia->delete(); // this deletes both DB record and physical file
            }
        }

        $user->update($inputs);
        $data = [
            'message' => __('message.userProfileUpdate'),
            'user' => new Resource($user),
        ];
        // }

        return $data;
    }

    public function changeStatus(object $user, array $inputs = [])
    {
        $wasInactive = $user->status != config('site.user_status.active');

        $inputs['email_verified_at'] = $inputs['status'] == config('site.user_status.active') ? now() : null;
        $user->update($inputs);

        $data = [
            'message' => __('entity.entityUpdated', ['entity' => 'User status']),
            'user' => new Resource($user),
        ];

        if ($inputs['status'] == config('site.user_status.active') && $wasInactive) {
            Mail::to($user->email)->send(new UserApprovedMail([
                'user' => $user,
                'name' => $user->name,
            ]));
        }
        return $data;
    }
}