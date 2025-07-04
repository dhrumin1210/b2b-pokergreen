<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use BaseModel, SoftDeletes;

    protected $fillable = [
        'user_id',
        'status',
        'address',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'timestamp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}