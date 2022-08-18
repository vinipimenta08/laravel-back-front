<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecoverPassword extends Model
{
    use HasFactory;

    protected $table = "recover_password";

    protected $fillable = [
        'id',
        'email',
        'token'
    ];

}
