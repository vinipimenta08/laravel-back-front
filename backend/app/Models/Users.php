<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Clients;
use App\Models\Profiles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Users extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use HasRoles;
    use SoftDeletes;

    protected $table = "users";

    protected $fillable = [
        'id',
        'id_client',
        'id_profile',
        'name',
        'email',
        'email_verified_at',
        'password',
        'active',
        'menuroles',
        'last_access',
        'alternative_profile',
        'enable_all'
    ];

    public function clients()
    {
        return $this->hasMany(Clients::class, 'id', 'id_client');
    }

    public function profiles()
    {
        return $this->hasMany(Profiles::class, 'id', 'id_profile');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
