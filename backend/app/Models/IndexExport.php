<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndexExport extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $fillable = [
        'id_list_custom',
        'crm_type',
        'index'
    ];
}
