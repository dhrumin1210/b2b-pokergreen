<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use BaseModel, HasFactory;

    protected $fillable = [
        'disk',
        'directory',
        'filename',
        'extension',
        'mime_type',
        'aggregate_type',
        'size',
        'created_at',
        'updated_at',
    ];

    public $queryable = [
        'id',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $relationship = [];

    protected $exactFilters = [];

    protected $scopedFilters = [];
}