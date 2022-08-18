<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSshExport extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $fillable = [
        'type',
        'message',
        'description',
        'name_file',
        'init_process',
        'init_search',
        'end_search',
        'init_make_file',
        'end_make_file',
        'init_upload',
        'end_upload'
    ];
}
