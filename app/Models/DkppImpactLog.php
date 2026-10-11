<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DkppImpactLog extends Model
{
    protected $table = 'dkpp_impact_logs';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
