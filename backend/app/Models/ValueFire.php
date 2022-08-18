<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValueFire extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';

    protected $fillable = [
        "qtd_min",
        "qtd_max",
        "value"
    ];
}
