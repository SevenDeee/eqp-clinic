<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $guarded = [];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function inventoryOrders()
    {
        return $this->hasMany(InventoryOrder::class);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class)
            ->using(InventoryOrder::class) // Use pivot model
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
