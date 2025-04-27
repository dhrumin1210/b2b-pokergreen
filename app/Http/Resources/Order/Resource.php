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
        $data['products'] = ProductResource::collection($this->resource->orderProducts->pluck('product'));
        
        return $data;
    }
}