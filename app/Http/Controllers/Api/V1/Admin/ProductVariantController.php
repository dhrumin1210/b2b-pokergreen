<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariant\BatchUpsert;
use App\Http\Requests\ProductVariant\Upsert;
use App\Http\Resources\ProductVariant\Collection as ProductVariantCollection;
use App\Http\Resources\ProductVariant\Resource as ProductVariantResource;
use App\Services\ProductVariantService;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductVariantController extends Controller
{
    use ApiResponser;

    protected ProductVariantService $productVariantService;

    public function __construct(ProductVariantService $productVariantService)
    {
        $this->productVariantService = $productVariantService;
    }

    public function index(Product $product, Request $request)
    {
        $variants = $this->productVariantService->collection($product, $request->all());

        return $this->collection(new ProductVariantCollection($variants));
    }

    public function store(Product $product, Upsert $request)
    {
        $variant = $this->productVariantService->create($product, $request->validated());

        return $this->resource(new ProductVariantResource($variant));
    }

    public function show(Product $product, ProductVariant $variant)
    {
        if ($variant->product_id !== $product->id) {
            abort(404, 'Variant not found for this product.');
        }
        return $this->resource(new ProductVariantResource($variant));
    }

    public function update(Product $product, Upsert $request, ProductVariant $variant)
    {
        $variant = $this->productVariantService->update($product, $variant, $request->validated());

        return $this->resource(new ProductVariantResource($variant));
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $this->productVariantService->delete($product, $variant);

        return response()->json(['message' => 'Product variant deleted successfully'], 200);
    }

    public function batchUpsert(BatchUpsert $request)
    {
        $this->productVariantService->batchUpsert($request->all());

        return response()->json([
            'message' => 'Variants batch upserted successfully!',
        ]);
    }
}
