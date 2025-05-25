<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use BaseModel;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_description',
        'product_variant_id',
        'variant_weight',
        'variant_unit',
        'weight',
        'unit',
        'total_weight',
        'quantity'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
