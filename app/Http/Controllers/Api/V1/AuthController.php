<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\AuthService;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login as LoginRequest;
use App\Http\Requests\Auth\SignUp as SignUpRequest;
use App\Http\Requests\Auth\SendOtp as SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtp as VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPassword as ResetPasswordRequest;
use App\Http\Requests\Auth\ChangePassword as ChangePasswordRequest;
use App\Http\Requests\Auth\ForgetPassword as ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordOtp as ResetPasswordOtpRequest;

class AuthController extends Controller
{
    use ApiResponser;

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService;
    }

    #[OA\Post(
        path: '/api/v1/signup',
        operationId: 'authSignup',
        tags: ['Auth'],
        summary: 'Register new user',
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Pass user credentials',
            content: new OA\JsonContent(
                required: ['name', 'mobile', 'email', 'address', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: 'Company Name'
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'test@gmail.com',
                    ),
                    new OA\Property(
                        property: 'mobile',
                        type: 'string',
                        example: '9974572182'
                    ),
                    new OA\Property(
                        property: 'address',
                        type: 'string',
                        example: 'Address'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        minLength: 6,
                        writeOnly: true,
                        description: "The user's password for login (not stored in plain text, consider using Laravel's `Hash` helper for secure storage)."
                    ),
                    new OA\Property(
                        property: 'password_confirmation',
                        type: 'string',
                        minLength: 6,
                        writeOnly: true,
                        description: "Confirmation of the user's password."
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
    )]
    public function signUp(SignUpRequest $request): JsonResponse
    {
        $data = $this->authService->signup($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/send-otp',
        operationId: 'sendOtp',
        tags: ['Auth'],
        summary: 'Send One-Time Password (OTP)',
        description: "Sends an OTP to a user's email address for verification purposes.",
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email and purpose for requesting OTP',
            content: new OA\JsonContent(
                required: ['email', 'otp_for'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
                    ),
                    new OA\Property(
                        property: 'otp_for',
                        type: 'string',
                        enum: ['register', 'forgot_password'],
                        example: 'forgot_password'
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
    )]
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $data = $this->authService->sendOtp($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/verify-otp',
        operationId: 'verifyOtp',
        tags: ['Auth'],
        summary: 'Verify One-Time Password (OTP)',
        description: 'Verifies an OTP submitted by a user for authentication or other purposes.',
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email and OTP code',
            content: new OA\JsonContent(
                required: ['email', 'otp'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
                    ),
                    new OA\Property(
                        property: 'otp',
                        type: 'string',
                        description: 'OTP code submitted by the user',
                        example: '123456',
                        minLength: 6,
                        maxLength: 6
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $data = $this->authService->verifyOtp($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/login',
        operationId: 'loginUser',
        tags: ['Auth'],
        summary: 'Login User',
        description: 'Logs in a user with email and password.',
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email and password',
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        description: "User's password",
                        example: 'password123',
                        minLength: 8,
                        maxLength: 255
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
    )]
    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/forget-password-otp',
        operationId: 'forgetPasswordWithOtp',
        tags: ['Auth'],
        summary: 'Forget Password with otp',
        description: "Initiates the process to reset the user's password by otp.",
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email',
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
    )]
    public function forgetPasswordOtp(ForgetPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->forgetPasswordOtp($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/reset-password-otp',
        operationId: 'resetPasswordWithOtp',
        tags: ['Auth'],
        summary: 'Reset Password',
        description: "Resets the user's password using the provided email, new password, and OTP code.",
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email, new password, and OTP code',
            content: new OA\JsonContent(
                required: ['email', 'password', 'password_confirmation', 'otp'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        description: "User's new password",
                        example: 'newpassword123',
                        minLength: 8,
                        maxLength: 255
                    ),
                    new OA\Property(
                        property: 'password_confirmation',
                        type: 'string',
                        description: "Confirmation of the user's new password",
                        example: 'newpassword123',
                        minLength: 8,
                        maxLength: 255
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
    )]
    public function resetPasswordOtp(ResetPasswordOtpRequest $request): JsonResponse
    {
        $data = $this->authService->resetPasswordOtp($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/change-password',
        operationId: 'changePassword',
        tags: ['Auth'],
        summary: 'Change Password',
        description: "Changes the user's password by verifying the current password and setting a new one.",
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Current password and new password',
            content: new OA\JsonContent(
                required: ['current_password', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(
                        property: 'current_password',
                        type: 'string',
                        description: "User's current password",
                        example: 'oldpassword123',
                        minLength: 8,
                        maxLength: 255
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        description: "User's new password",
                        example: 'newpassword123',
                        minLength: 8,
                        maxLength: 255
                    ),
                    new OA\Property(
                        property: 'password_confirmation',
                        type: 'string',
                        description: "Confirmation of the user's new password",
                        example: 'newpassword123',
                        minLength: 8,
                        maxLength: 255
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $data = $this->authService->changePassword($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/logout',
        operationId: 'logoutUser',
        tags: ['Auth'],
        summary: 'Logout User',
        description: 'Logs out the currently authenticated user.',
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function logout(): JsonResponse
    {
        $data = $this->authService->logout();

        return $this->success($data, 200);
    }
}