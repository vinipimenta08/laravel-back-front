<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplySms extends Model
{
    use HasFactory;

    protected $table = "reply_sms";
    protected $connection = 'mysql2';

    protected $fillable = [
        'idReferencia',
        'phone',
        'idSMS',
        'reply',
        'received_at',
        'id_list_custom'
    ];
}
