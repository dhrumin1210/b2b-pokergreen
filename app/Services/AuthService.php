<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\UserOtp;
use App\Jobs\SendOtpMail;
use Illuminate\Support\Str;
use App\Jobs\VerifyUserMail;
use Illuminate\Support\Facades\DB;
use App\Jobs\ForgetPasswordOtpMail;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\User\Resource as UserResource;
use App\Mail\ForgetPasswordOtp;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserApprovalNotification;

class AuthService
{
    private User $userObj;

    private UserOtp $userOtpObj;

    private UserOtpService $userOtpService;

    public function __construct()
    {
        $this->userObj = new User;

        $this->userOtpObj = new UserOtp;

        $this->userOtpService = new UserOtpService;
    }

    public function signup(array $inputs): array
    {
        $inputs['role_id'] = config('site.roleIds.user');
        $user = $this->userObj->create($inputs);
        $user->assignRole(config('site.user_role.user'));

        // Send notification to admins
        $admins = $this->userObj->where('role_id', 1)->get();
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new UserApprovalNotification([
                    'name' => $admin->name,
                    'user' => $user,
                ]));
            } catch (\Exception $e) {
                Log::error('Failed to send user approval notification to admin: ' . $e->getMessage());
            }
        }

        $data['message'] = __('message.userSignUpSuccess');
        $data['data'] = new UserResource($user);
        return $data;
    }

    public function login(array $inputs, bool $isAdmin = false): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        if (!$user || !Hash::check($inputs['password'], $user->password)) {
            throw new CustomException(__('auth.failed'));
        }

        if ($user->status == config('site.user_status.inactive')) {
            throw new CustomException(__('message.inactiveUser'));
        }

        // Check if user is trying to access admin panel
        if ($isAdmin) {
            if ($user->role_id != config('site.roleIds.admin')) {
                throw new CustomException(__('message.unauthorizedAdminAccess'));
            }
        }

        $data = [
            'message' => 'Login successfully',
            'user' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];

        return $data;
    }

    public function forgetPasswordOtp(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();
        if (empty($user)) {
            throw new CustomException(__('message.emailNotExist'));
        }

        $this->userOtpObj->where('user_id', $user['id'])->where('otp_for', 'forgot_password')->delete();

        $otp = Helper::generateOTP(config('site.generate_otp_length'));
        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'forgot_password']);

        try {
            Mail::to($user->email)->send(new ForgetPasswordOtp([
                'otp' => $otp,
                'name' => $user->name,
            ]));
        } catch (\Exception $e) {
            Log::info('Forget Password mail failed.' . $e->getMessage());
        }

        $data = [
            'message' => __('message.forgetPasswordEmailSuccess'),
        ];

        return $data;
    }

    public function sendOtp(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();
        if (empty($user)) {
            throw new CustomException(__('message.emailNotExist'));
        }

        $otp = Helper::generateOTP(config('site.otp.length'));

        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user['id'], 'otp_for' => $inputs['otp_for']]);

        $mailClass = match ($inputs['otp_for']) {
            config('site.otp.types.forgot_password') => ForgetPasswordOtp::class,
            default => null,
        };

        try {
            if ($mailClass) {
                Mail::to($user->email)->send(new $mailClass([
                    'otp' => $otp,
                    'name' => $user->name,
                ]));
            }
        } catch (\Exception $e) {
            Log::info('Send Otp mail failed.' . $e->getMessage());
        }

        $data = [
            'message' => 'Otp Send Successfully',
        ];

        return $data;
    }

    public function verifyOtp(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        if (empty($user)) {
            throw new CustomException(__('message.emailNotExist'));
        }

        $userOtp = $this->userOtpService->otpExists($user['id'], $inputs['otp'], 'forgot_password');
        if (empty($userOtp)) {
            throw new CustomException(__('message.invalidOtp'));
        }

        $isExpired = $this->userOtpService->isOtpExpired($userOtp['created_at'], $userOtp['verified_at']);

        if ($isExpired) {
            throw new CustomException(__('message.otpExpired'));
        }

        $this->userOtpService->update($userOtp['id'], ['verified_at' => date('Y-m-d h:i:s')]);
        $this->userObj->where('id', $user['id'])->update(['email_verified_at' => date('Y-m-d h:i:s')]);

        $data['message'] = __('message.userVerifySuccess');
        $data['redirect_to'] = 'reset_password';
        $data['data'] = new UserResource($user);

        return $data;
    }


    public function resetPasswordOtp(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        if (empty($user)) {
            throw new CustomException(__('message.emailNotExist'));
        }

        $user->update(['password' => $inputs['password']]);

        $data['message'] = __('message.passwordChangeSuccess');
        return $data;
    }


    public function changePassword(array $inputs): array
    {
        $user = User::find(auth()->id());
        $currentPassword = trim($inputs['current_password']);
        $newPassword = trim($inputs['password']);

        if (strcmp($currentPassword, $newPassword) == 0) {
            throw new CustomException(__('message.newPasswordMatchedWithCurrentPassword'));
        }

        if (!Hash::check($inputs['current_password'], $user->password)) {
            throw new CustomException(__('message.wrongCurrentPassword'));
        }

        $user->password = $newPassword;
        $user->save();

        $data['message'] = __('message.passwordChangeSuccess');

        return $data;
    }

    public function logout(): array
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        }

        $data['message'] = __('message.logoutSuccess');
        return $data;
    }
}