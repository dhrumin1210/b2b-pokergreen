<?php

namespace App\Models;

use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use BaseModel, Mediable, HasSlug, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'status'];

    protected $relationship = [
        'media' => [
            'model' => Media::class,
        ]
    ];

    protected $scopedFilters = [
        'search',
    ];

    protected $defaultSorts = '-id';


    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }
}