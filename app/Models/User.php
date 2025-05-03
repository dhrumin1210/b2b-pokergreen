<?php

namespace App\Models;

use App\Traits\BaseModel;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Plank\Mediable\Mediable;

class User extends Authenticatable
{
    use BaseModel, HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, Mediable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'status',
        'password',
        'mobile',
        'address',
        'email_verified_at',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // protected $dates = ['created_at'];

    protected $relationship = [
        'media' => [
            'model' => Media::class,
        ]
    ];

    protected $guard_name = 'api';

    protected $appends = ['display_status'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }

    public function displayStatus(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value == config('site.user_status.active') ? 'Active' : 'Inactive',
        );
    }
}