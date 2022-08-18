<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckExistsWhatsApp extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = "check_exists_whatsApp";

    protected $fillable = [
        'id',
        'id_genion',
        'ddd',
        'phone'
    ];

}
