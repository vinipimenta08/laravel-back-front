<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    protected $table = 'menus';
    public $timestamps = false; 

    protected $fillable = [
        'name',
        'href',
        'icon',
        'slug',
        'locateicon',
        'parent_id',
        'menu_id',
        'locate-icon',
        'sequence',
    ];

    public function roles()
    {
        return $this->hasMany(Menurole::class);
    }
}
