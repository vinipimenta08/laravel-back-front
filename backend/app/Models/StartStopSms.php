<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartStopSms extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'start_stop_sms';
    public $timestamps = false;

    protected $fillable = [
        'hash_id_campaign',
        'run'
    ];
}