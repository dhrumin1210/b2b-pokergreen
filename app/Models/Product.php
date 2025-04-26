<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use BaseModel,Mediable, HasSlug, SoftDeletes;
    
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    //Relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Scope
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

    public function scopeCategoryId($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
