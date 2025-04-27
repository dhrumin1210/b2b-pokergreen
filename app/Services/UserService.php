<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\Helper;
use App\Jobs\VerifyUserMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User\Resource;
use App\Mail\UserApprovedMail;
use Illuminate\Support\Facades\Mail;

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

        if (!empty($inputs['email']) && $inputs['email'] != $user->email) {
            $inputs['email_verified_at'] = null;
            $otp = Helper::generateOTP(config('site.generate_otp_length'));
            $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'verification']);

            try {
                VerifyUserMail::dispatch($user, $otp);
            } catch (\Exception $e) {
                Log::info('User verification mail failed.' . $e->getMessage());
            }

            $user->update($inputs);
            $data = [
                'message' => __('message.updateUserVerifySuccess'),
                'user' => new Resource($user),
            ];
        } else {
            $user->update($inputs);
            $data = [
                'message' => __('message.userProfileUpdate'),
                'user' => new Resource($user),
            ];
        }

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