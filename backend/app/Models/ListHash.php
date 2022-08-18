<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ListCustom;
use App\Models\StatusLink;

class ListHash extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = "list_hash";

    protected $fillable = [
        'id',
        'id_list_custom',
        'hash',
        'id_status_link'
    ];

    public function listCustom()
    {
        return $this->hasOne(ListCustom::class, 'id', 'id_list_custom');
    }

    public function statusLink()
    {
        return $this->hasMany(StatusLink::class, 'id', 'id_status_link');
    }
    
}
