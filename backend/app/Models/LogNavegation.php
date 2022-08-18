<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogNavegation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ip',
        'route',
        'method',
        'id_user'
    ];
}
