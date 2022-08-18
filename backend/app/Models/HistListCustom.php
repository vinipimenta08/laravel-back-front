<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clients;
use App\Models\SMSCampaigns;

class HistListCustom extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = "hist_list_custom";

    protected $fillable = [
        'id',
        'id_client',
        'id_campaign',
        'name_client',
        'name_campaign',
        'base',
        'sended',
        'opening',
        'imported',
        'failed',
        'reply',
        'sended_at',
        'location',
        'last_days'
    ];

    public function clients()
    {
        return $this->hasMany(Clients::class, 'id', 'id_client');
    }

    public function smsCampaigns()
    {
        return $this->belongsTo(SMSCampaigns::class, 'id_campaigns');
    }

    

}
