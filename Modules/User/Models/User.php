<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, SoftDeletes, HasUuids;

    protected $keyType = 'string';
    protected $fillable = ['name', 'email', 'role_id'];
    protected $hidden = [];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
