<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Services\AuthService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Auth\UpdateProfile;

class AuthController extends Controller
{
    use ApiResponser;

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService;
    }

    #[OA\Post(
        path: '/api/v1/admin/login',
        tags: ['Admin / Auth'],
        operationId: 'adminLogin',
        summary: 'Admin login',
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
            content: new OA\JsonContent(
                type: 'object',
                required: ['email', 'password'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        description: 'Admin email address',
                        example: 'pokergreen-admin@yopmail.com'

                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        description: 'Admin password',
                        example: 'Password@123#'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
    )]
    public function login(Login $request)
    {
        $response = $this->authService->login($request->all(), $isAdmin = true);
        return $this->success($response);
    }

   
}