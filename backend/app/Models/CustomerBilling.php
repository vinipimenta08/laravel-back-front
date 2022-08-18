<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerBilling extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'mysql2';
    protected $table = "customer_billing";

    protected $fillable = [
        'id',
        'id_client',
        'value',
        'active'
    ];

    public function clients()
    {
        return $this->hasMany(Clients::class, 'id', 'id_client');
    }

}
