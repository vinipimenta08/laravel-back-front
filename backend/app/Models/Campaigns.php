<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clients;
use App\Models\SMSCampaigns;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaigns extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'mysql2';
    protected $table = "campaigns";

    protected $fillable = [
        'id',
        'id_client',
        'name',
        'active'
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
