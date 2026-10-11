<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RescueItem extends Model
{
    protected $table = 'rescue_items';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'item_id');
    }
}
