<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = "blacklist";

    protected $fillable = [
        'id',
        'mailing_file_original',
        'mailing_file_genion',
        'id_client',
        'ddd',
        'phone',
        'active'
    ];


    public function clients()
    {
        return $this->hasMany(Clients::class, 'id', 'id_client');
    }
}
