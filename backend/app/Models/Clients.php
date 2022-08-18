<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Clients extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'mysql2';
    protected $table = "clients";

    protected $fillable = [
        'id',
        'name',
        'password',
        'contact',
        'broker_sms',
        'just_sms',
        'active',
    ];

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

    public function user()
    {
        return $this->belongsTo(Users::class, 'id', 'id_client');
    }
}
