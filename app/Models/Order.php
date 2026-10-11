<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(RescueItem::class, 'item_id');
    }

    public function escrow()
    {
        return $this->hasOne(EscrowLedger::class, 'order_id');
    }

    public function impact()
    {
        return $this->hasOne(DkppImpactLog::class, 'order_id');
    }
}
