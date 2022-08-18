<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogImport extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';

    protected $fillable = [
        'id_user',
        'id_client',
        'id_campaign',
        'qtd_import',
        'send_sms'
    ];
}
