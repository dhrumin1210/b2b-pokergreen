<?php

namespace App\Http\Resources\Order;

use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    use ResourceFilterable;

    public $collects = Resource::class;

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}