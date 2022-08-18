<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogImportError extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = "log_import_errors";

    protected $fillable = [
        'id',
        'id_client',
        'id_campaigns',
        'line_file',
        'name_file',
        'qtd_errors',
        'fields_errors',
        'date_input'
    ];
}
