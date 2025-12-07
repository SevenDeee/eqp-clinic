<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    //
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventoryOrders()
    {
        return $this->hasMany(InventoryOrder::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->using(InventoryOrder::class) // Use pivot model
            ->withPivot('quantity')
            ->withTimestamps();
    }

}
