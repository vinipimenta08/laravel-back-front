<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsSendingProgram extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'mysql2';
    protected $table = "sms_sending_program";

    protected $fillable = [
        'id',
        'mailing_file_original',
        'mailing_file_genion',
        'id_client',
        'id_campaign',
        'researched',
        'programmed_at',
        'active'
    ];
}
