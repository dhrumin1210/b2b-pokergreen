<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use BaseModel, SoftDeletes;

    protected $fillable = [
        'product_id',
        'weight',
        'unit',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}