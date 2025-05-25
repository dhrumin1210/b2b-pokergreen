<?php

namespace App\Http\Resources\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Media\Resource as MediaResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Category::class;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();

        $media = $this->resource->getMedia('featured')->first();

        $data['media']['url'] = $media?->getUrl();

        return $data;
    }
}