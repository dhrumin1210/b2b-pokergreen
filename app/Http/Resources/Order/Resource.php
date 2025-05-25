<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Resources\Product\Resource as ProductResource;
use App\Models\Order;
use App\Traits\ResourceFilterable;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Order::class;

    public function toArray($request)
    {
        $data = $this->fields();
        $data['user'] = new UserResource($this->resource->user);
        $data['address'] = $this->resource->address;

        $data['products'] = $this->resource->orderProducts->map(function ($orderProduct) use ($request) {
            $product = $orderProduct->product;
            $category = $product?->category;
            $isProductDeleted = !$product;
            $isVariantDeleted = !$orderProduct->productVariant;

            $productArray = [
                'id' => $orderProduct->product_id,
                'category_id' => $category?->id ?? null,
                'name' => $isProductDeleted ? $orderProduct->product_name : $product->name,
                'slug' => $product?->slug ?? null,
                'description' => $isProductDeleted ? $orderProduct->product_description : $product->description,
                'status' => $product?->status ?? false,
                'is_deleted' => $isProductDeleted,
                'is_variant_deleted' => $isVariantDeleted
            ];

            if ($product) {
                $media = $product->getMedia('featured')->first();
                if ($media) {
                    $productArray['media'] = [
                        'id' => $media->id,
                        'disk' => $media->disk,
                        'directory' => $media->directory,
                        'filename' => $media->filename,
                        'extension' => $media->extension,
                        'mime_type' => $media->mime_type,
                        'aggregate_type' => $media->aggregate_type,
                        'size' => $media->size,
                        'variant_name' => $media->variant_name,
                        'original_media_id' => $media->original_media_id,
                        'created_at' => $media->created_at,
                        'updated_at' => $media->updated_at,
                        'alt' => $media->alt,
                        'url' => $media->getUrl(),
                        'pivot' => [
                            'mediable_type' => get_class($product),
                            'mediable_id' => $product->id,
                            'media_id' => $media->id,
                            'tag' => 'featured',
                            'order' => 1
                        ]
                    ];
                }
            }

            $variant = $orderProduct->productVariant;
            $productArray['variants'] = [
                'product_id' => $orderProduct->product_id,
                'product_variant_id' => $orderProduct->product_variant_id,
                'weight' =>$orderProduct->weight,
                'unit' => $orderProduct->variant_unit,
                'quantity' => $orderProduct->quantity,
                'total_weight' => $orderProduct->total_weight
            ];

            if ($category) {
                $categoryMedia = $category->getMedia('featured')->first();
                $productArray['category'] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'status' => $category->status,
                    'media' => [
                        'url' => $categoryMedia?->getUrl()
                    ]
                ];
            }

            return $productArray;
        });

        return $data;
    }
}
