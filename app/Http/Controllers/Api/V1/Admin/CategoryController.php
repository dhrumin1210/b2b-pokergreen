<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\Upsert;
use App\Http\Resources\Category\Resource;
use App\Http\Resources\Category\Collection;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    use ApiResponser;

    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    #[OA\Get(
        path: '/api/v1/admin/categories',
        tags: ['Admin / Category'],
        summary: 'List all categories',
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            ),
            new OA\Parameter(
                name: 'media',
                in: 'query',
                description: 'Include media: `featured`',
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success.'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function index(Request $request)
    {
        $categories = $this->categoryService->collection($request->all());

        return $this->collection(new Collection($categories));
    }

    #[OA\Post(
        path: '/api/v1/admin/categories',
        tags: ['Admin / Category'],
        summary: 'Create a new category',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['name'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Poker'),
                        new OA\Property(property: 'slug', type: 'string', example: 'poker'),
                        new OA\Property(property: 'description', type: 'string', example: 'Poker category'),
                        new OA\Property(property: 'media_id', type: 'integer', example: 1),
                        new OA\Property(property: 'media', type: 'string', format: 'binary', description: 'Image file (jpg, jpeg, png, gif, webp)')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Category created.'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function store(Upsert $request)
    {
        $category = $this->categoryService->create($request->validated());

        return $this->resource(new Resource($category));
    }

    #[OA\Get(
        path: '/api/v1/admin/categories/{id}',
        tags: ['Admin / Category'],
        summary: 'Get a single category',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success.'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function show(int $id)
    {
        $category = $this->categoryService->resource($id);

        return $this->resource(new Resource($category));
    }

    #[OA\Put(
        path: '/api/v1/admin/categories/{id}',
        tags: ['Admin / Category'],
        summary: 'Update a category',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Poker'),
                        new OA\Property(property: 'slug', type: 'string', example: 'poker'),
                        new OA\Property(property: 'description', type: 'string', example: 'Poker category'),
                        new OA\Property(property: 'media_id', type: 'integer', example: 1),
                        new OA\Property(property: 'media', type: 'string', format: 'binary', description: 'Image file (jpg, jpeg, png, gif, webp)')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Category updated.'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function update(Upsert $request, int $id)
    {
        $category = $this->categoryService->update($id, $request->validated());

        return $this->resource(new Resource($category));
    }

    #[OA\Delete(
        path: '/api/v1/admin/categories/{id}',
        tags: ['Admin / Category'],
        summary: 'Delete a category',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Category deleted.'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function destroy(int $id)
    {
        $this->categoryService->delete($id);

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}