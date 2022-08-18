<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMenus extends Model
{
    use HasFactory;
    protected $table = 'user_menus';

    protected $fillable = [
        'id_user',
        'id_menu'
    ];

}
