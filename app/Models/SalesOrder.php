<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'so_number', 'order_date', 'customer', 'status'
    ];

    // Relasi: Satu SO punya banyak item
    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function getTotalAttribute()
    {
        return $this->items->sum('total_price');
    }
    
}
