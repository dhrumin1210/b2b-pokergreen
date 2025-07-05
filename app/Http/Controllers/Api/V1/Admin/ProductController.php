<?php

namespace App\Http\Controllers\Api\V1\Admin;

use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Upsert;
use App\Http\Resources\Product\Resource;
// use App\Http\Resources\ProductResource;
use App\Http\Resources\Product\Collection;

#[OA\Tag(name: 'Admin / Products')]
class ProductController extends Controller
{
    use ApiResponser;
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[OA\Get(
        path: '/api/v1/admin/products',
        tags: ['Admin / Products'],
        operationId: 'getProducts',
        summary: 'Get all products',
        security: [[
            'bearerAuth' => [],
        ]],
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
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '401', description: 'Unauthorized'),
        ],
    )]
    public function index(Request $request)
    {
        $products = $this->productService->collection($request->all());
        return $this->collection(new Collection($products));
    }

    #[OA\Get(
        path: '/api/v1/admin/products/{id}',
        tags: ['Admin / Products'],
        operationId: 'getProduct',
        summary: 'Get a specific product',
        security: [[
            'bearerAuth' => [],
        ]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Product ID',
                schema: new OA\Schema(type: 'integer')
            ),
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
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '401', description: 'Unauthorized'),
            new OA\Response(response: '404', description: 'Product not found'),
        ],

    )]
    public function show($id)
    {
        $product = $this->productService->resource($id);
        return $this->resource(new Resource($product));
    }

    #[OA\Post(
        path: '/api/v1/admin/products',
        tags: ['Admin / Products'],
        operationId: 'createProduct',
        summary: 'Create a new product',
        security: [[
            'bearerAuth' => [],
        ]],
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
                required: ['name', 'slug', 'category_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Product Name'),
                    new OA\Property(property: 'slug', type: 'string', example: 'product-name'),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'description', type: 'string', example: 'Product description'),
                    new OA\Property(property: 'status', type: 'boolean', example: true),
                    new OA\Property(property: 'media', type: 'string', format: 'binary'),
                    new OA\Property(property: 'media_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: '201',
                description: 'Product created successfully.',
            ),
            new OA\Response(response: '401', description: 'Unauthorized'),
            new OA\Response(response: '422', description: 'Validation errors'),
        ],
    )]
    public function store(Upsert $request)
    {
        $product = $this->productService->create($request->all());
        return $this->resource(new Resource($product));
    }

    #[OA\Put(
        path: '/api/v1/admin/products/{id}',
        tags: ['Admin / Products'],
        operationId: 'updateProduct',
        summary: 'Update a product',
        security: [[
            'bearerAuth' => [],
        ]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Product ID',
                schema: new OA\Schema(type: 'integer')
            ),
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
                required: ['name', 'slug', 'category_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Updated Product Name'),
                    new OA\Property(property: 'slug', type: 'string', example: 'updated-product-name'),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'description', type: 'string', example: 'Updated product description'),
                    new OA\Property(property: 'status', type: 'boolean', example: true),
                    new OA\Property(property: 'media', type: 'string', format: 'binary'),
                    new OA\Property(property: 'media_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Product updated successfully.',
            ),
            new OA\Response(response: '401', description: 'Unauthorized'),
            new OA\Response(response: '404', description: 'Product not found'),
            new OA\Response(response: '422', description: 'Validation errors'),
        ],
    )]
    public function update(Upsert $request, $id)
    {
        $product = $this->productService->update($id, $request->all());
        return $this->resource(new Resource($product));
    }

    #[OA\Delete(
        path: '/api/v1/admin/products/{id}',
        tags: ['Admin / Products'],
        operationId: 'deleteProduct',
        summary: 'Delete a product',
        security: [[
            'bearerAuth' => [],
        ]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Product ID',
                schema: new OA\Schema(type: 'integer')
            ),
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
        responses: [
            new OA\Response(
                response: '200',
                description: 'Product deleted successfully.',
            ),
            new OA\Response(response: '401', description: 'Unauthorized'),
            new OA\Response(response: '404', description: 'Product not found'),
        ],
    )]
    public function destroy($id)
    {
        $this->productService->delete($id);
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}