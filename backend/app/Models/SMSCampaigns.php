<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Campaigns;

class SMSCampaigns extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = "sms_campaigns";

    protected $fillable = [
        'id_campaigns',
        'send',
        'deliver',
        'fail',
        'response'
    ];

    public function campaigns()
    {
        return $this->hasOne(Campaigns::class, 'id_campaigns');
    }
}
