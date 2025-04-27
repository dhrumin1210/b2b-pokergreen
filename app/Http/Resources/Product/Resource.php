<?php

namespace App\Http\Resources\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Category\Resource as CategoryResource;
use App\Http\Resources\ProductVariant\Resource as ProductVariantResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Product::class;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();
        $media = $this->resource->getMedia('featured')->first();

        $data['media'] = $media;
        $data['media']['url'] = $media?->getUrl();
        $data['variants'] = ProductVariantResource::collection($this->resource->variants);
        $data['category'] = new CategoryResource($this->resource->category);

        return $data;
    }
}
