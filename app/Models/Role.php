<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Role extends Model
{
    use BaseModel;

    protected $table = 'roles';

    protected $fillable = [
        'name',
    ];

    public $queryable = [
        'id',
    ];

    protected $casts = [];

    protected $relationship = [];
}