<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\UserService;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\UpdateProfile;
use App\Http\Resources\User\Resource as UserResource;

class UserController extends Controller
{
    use ApiResponser;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    #[OA\Get(
        path: '/api/v1/me',
        tags: ['Auth'],
        summary: 'Get logged-in user details',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
        ],
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
            new OA\Parameter(
                name: 'media',
                in: 'query',
                description: 'For Include A Media : `profile`'
            ),
        ],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function me(): JsonResponse
    {
        $user = $this->userService->resource(Auth::id());

        return $this->resource(new UserResource($user));
    }

    #[OA\Post(
        path: '/api/v1/me',
        operationId: 'updateProfile',
        tags: ['Auth'],
        summary: 'Update Profile',
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
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        type: 'object',
                        properties: [
                            new OA\Property(
                                property: 'name',
                                type: 'string',
                                example: 'John Doe'
                            ),
                            new OA\Property(
                                property: 'mobile',
                                type: 'string',
                                example: '1234567890'
                            ),
                            new OA\Property(
                                property: 'profile_image',
                                type: 'string',
                                format: 'binary',
                                description: 'Profile image file (jpg, jpeg, png, gif)'
                            ),
                        ]
                    )
                )
            ]
        ),
        responses: [
            new OA\Response(response: '200', description: 'Profile updated successfully'),
            new OA\Response(response: '400', description: 'Validation errors'),
            new OA\Response(response: '401', description: 'Unauthorized'),
        ],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function updateProfile(UpdateProfile $request): JsonResponse
    {
        $data = $this->userService->update(Auth::id(), $request->validated());

        return $this->success($data, 200);
    }
}