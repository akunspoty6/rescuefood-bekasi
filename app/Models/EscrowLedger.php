<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscrowLedger extends Model
{
    protected $table = 'escrow_ledger';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
