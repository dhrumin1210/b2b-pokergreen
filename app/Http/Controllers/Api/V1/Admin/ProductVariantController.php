<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Product;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use App\Services\ProductVariantService;
use App\Http\Requests\ProductVariant\Upsert;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\ProductVariant\BatchUpsert;
use App\Http\Resources\ProductVariant\Resource as ProductVariantResource;
use App\Http\Resources\ProductVariant\Collection as ProductVariantCollection;

class ProductVariantController extends Controller
{
    use ApiResponser;

    protected ProductVariantService $productVariantService;

    public function __construct(ProductVariantService $productVariantService)
    {
        $this->productVariantService = $productVariantService;
    }

    #[OA\Get(
        path: '/api/v1/admin/products/{product}/variants',
        tags: ['Admin / Product Variant'],
        summary: 'List all variants for a product',
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'Product ID'
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
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function index(Product $product, Request $request)
    {
        $variants = $this->productVariantService->collection($product, $request->all());

        return $this->collection(new ProductVariantCollection($variants));
    }

    #[OA\Post(
        path: '/api/v1/admin/products/{product}/variants',
        tags: ['Admin / Product Variant'],
        summary: 'Create a new product variant',
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'Product ID'
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
                mediaType: 'application/json',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['weight', 'unit'],
                    properties: [
                        new OA\Property(property: 'weight', type: 'number', example: 1.5),
                        new OA\Property(property: 'unit', type: 'string', example: 'kg', enum: ['kg', 'g', 'gm', 'pc', 'pcs'])
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product variant created.'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function store(Product $product, Upsert $request)
    {
        $variant = $this->productVariantService->create($product, $request->validated());

        return $this->resource(new ProductVariantResource($variant));
    }

    #[OA\Get(
        path: '/api/v1/admin/products/{product}/variants/{variant}',
        tags: ['Admin / Product Variant'],
        summary: 'Get a single product variant',
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'Product ID'
            ),
            new OA\Parameter(
                name: 'variant',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'Variant ID'
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
    public function show(Product $product, ProductVariant $variant)
    {
        if ($variant->product_id !== $product->id) {
            abort(404, 'Variant not found for this product.');
        }
        return $this->resource(new ProductVariantResource($variant));
    }

    #[OA\Put(
        path: '/api/v1/admin/products/{product}/variants/{variant}',
        tags: ['Admin / Product Variant'],
        summary: 'Update a product variant',
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'Product ID'
            ),
            new OA\Parameter(
                name: 'variant',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'Variant ID'
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
                mediaType: 'application/json',
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'weight', type: 'number', example: 1.5),
                        new OA\Property(property: 'unit', type: 'string', example: 'kg', enum: ['kg', 'g', 'gm', 'pc', 'pcs'])
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Product variant updated.'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function update(Product $product, Upsert $request, ProductVariant $variant)
    {
        $variant = $this->productVariantService->update($product, $variant, $request->validated());

        return $this->resource(new ProductVariantResource($variant));
    }

    #[OA\Delete(
        path: '/api/v1/admin/products/{product}/variants/{variant}',
        tags: ['Admin / Product Variant'],
        summary: 'Delete a product variant',
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'Product ID'
            ),
            new OA\Parameter(
                name: 'variant',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'Variant ID'
            ),
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string', default: 'XMLHttpRequest')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product variant deleted.'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function destroy(Product $product, ProductVariant $variant)
    {
        $this->productVariantService->delete($product, $variant);

        return response()->json(['message' => 'Product variant deleted successfully'], 200);
    }

    #[OA\Post(
        path: '/api/v1/admin/product-variants/batch-upsert',
        tags: ['Admin / Product Variant'],
        summary: 'Batch upsert product variants',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['product_id', 'variants'],
                    properties: [
                        new OA\Property(property: 'product_id', type: 'integer', example: 1),
                        new OA\Property(
                            property: 'variants',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'variant_id', type: 'integer', example: 1),
                                    new OA\Property(property: 'weight', type: 'number', example: 1.5),
                                    new OA\Property(property: 'unit', type: 'string', example: 'kg', enum: ['kg', 'gm', 'pc'])
                                ]
                            )
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Variants batch upserted successfully!'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function batchUpsert(BatchUpsert $request)
    {
        $this->productVariantService->batchUpsert($request->all());

        return response()->json([
            'message' => 'Variants batch upserted successfully!',
        ]);
    }
}