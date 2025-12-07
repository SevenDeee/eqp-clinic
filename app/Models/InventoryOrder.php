<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InventoryOrder extends Pivot
{
    protected $table = 'inventory_order';
    protected $guarded = [];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
