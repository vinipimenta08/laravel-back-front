<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLink extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = "send_sms";

    protected $fillable = [
        'status',
        'active',
    ];
}
