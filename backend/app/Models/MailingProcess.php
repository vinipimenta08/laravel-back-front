<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clients;
use App\Models\Campaigns;
use App\Models\StatusLink;


class MailingProcess extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = "mailing_process";

    protected $fillable = [
        'id',
        'mailing_file_original',
        'mailing_file_genion',
        'id_client',
        'id_campaign',
        'ddd',
        'phone',
        'message_sms',
        'date_event',
        'title',
        'description',
        'location',
        'joker_one',
        'joker_two',
        'identification',
        'send_sms',
        'id_sms',
        'id_status_link',
        'hash'
    ];

    public function client()
    {
        return $this->hasOne(Clients::class, 'id', 'id_client');
    }

    public function campaign()
    {
        return $this->hasMany(Campaigns::class, 'id', 'id_campaign');
    }

    public function statusLink()
    {
        return $this->hasMany(StatusLink::class, 'id', 'id_status_link');
    }
}
