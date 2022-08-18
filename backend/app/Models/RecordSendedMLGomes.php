<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordSendedMLGomes extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = "record_sended_ml_gomes";

    protected $fillable = [
        'id',
        'id_list_custom',
        'identification',
        'phone'
    ];

}
