<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class LogUpdate extends Model
{
    use HasFactory;
    protected $table = 'log_update';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'table',
        'id_register',
        'field',
        'old_value',
        'new_value',
        'modified_at'
    ];

    public function users()
    {
        return $this->hasMany(Users::class, 'id', 'id_user');
    }
}
