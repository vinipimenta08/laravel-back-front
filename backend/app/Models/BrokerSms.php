<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrokerSms extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'mysql2';
    protected $table = "brokers_sms";

    protected $fillable = [
        'id',
        'name',
        'active'
    ];
}
