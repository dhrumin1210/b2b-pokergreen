<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class ProductVariantService
{
    use PaginationTrait;

    private ProductVariant $productVariantObj;

    public function __construct()
    {
        $this->productVariantObj = new ProductVariant;
    }

    public function collection(Product $product, array $inputs)
    {
        $query = $product->variants()->newQuery();

        if (isset($inputs['search'])) {
            $query->where('weight', 'like', '%' . $inputs['search'] . '%')
                ->orWhere('unit', 'like', '%' . $inputs['search'] . '%');
        }

        return $this->paginationAttribute($query, $inputs);
    }

    public function resource(Product $product, ProductVariant $variant)
    {
        if ($variant->product_id !== $product->id) {
            throw new CustomException(__('entity.entityMismatch', ['entity' => 'Product Variant']), 404);
        }

        return $variant;
    }

    public function create(Product $product, array $inputs)
    {
        if (isset($inputs['variants']) && is_array($inputs['variants'])) {
            return DB::transaction(function () use ($product, $inputs) {
                $variants = [];
                foreach ($inputs['variants'] as $variant) {
                    $variants[] = $product->variants()->create($variant);
                }
                return $variants;
            });
        }

        return $product->variants()->create($inputs);
    }

    public function update(Product $product, ProductVariant $variant, array $inputs)
    {
        if ($variant->product_id !== $product->id) {
            throw new CustomException(__('entity.entityMismatch', ['entity' => 'Product Variant']), 404);
        }

        $variant->update($inputs);

        return $variant;
    }

    public function delete(Product $product, ProductVariant $variant): bool
    {
        if ($variant->product_id !== $product->id) {
            throw new CustomException(__('entity.entityMismatch', ['entity' => 'Product Variant']), 404);
        }

        if ($variant->orderProducts()->exists()) {
            throw new CustomException('Cannot delete product variant because it has associated orders', 400);
        }

        return $variant->delete();
    }

    public function batchUpsert(array $inputs)
    {
        $productId = $inputs['product_id'];
        $variants = $inputs['variants'];

        $product = DB::table('products')->where('id', $productId)->first();
        if (!$product) {
            throw new CustomException(__('entity.entityNotFound', ['entity' => 'Product']), 404);
        }

        DB::beginTransaction();

        try {
            foreach ($variants as $variant) {
                if (!empty($variant['variant_id'])) {
                    $existingVariant = ProductVariant::where('id', $variant['variant_id'])
                        ->where('product_id', $productId)
                        ->first();

                    if (!$existingVariant) {
                        throw new CustomException(__('entity.entityNotFound', ['entity' => 'Product Variant']), 404);
                    }

                    $existingVariant->update([
                        'weight' => $variant['weight'],
                        'unit' => $variant['unit'],
                    ]);
                } else {
                    ProductVariant::create([
                        'product_id' => $productId,
                        'weight' => $variant['weight'],
                        'unit' => $variant['unit'],
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}