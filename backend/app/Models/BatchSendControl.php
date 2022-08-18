<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clients;
use App\Models\Campaigns;
use App\Models\StatusLink;


class BatchSendControl extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = "batch_send_control";

    protected $fillable = [
        'id',
        'mailing_file_original',
        'mailing_file_genion',
        'id_sms',
        'verified',
        'search_date'
    ];

}
