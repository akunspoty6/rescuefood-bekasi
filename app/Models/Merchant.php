<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $table = 'merchants';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function rescueItems()
    {
        return $this->hasMany(RescueItem::class, 'merchant_id');
    }
}
