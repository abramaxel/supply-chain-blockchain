<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $fillable = [
        'sales_order_id', 'item_id', 'batch_no', 'quantity', 'unit_selling_price', 'total_price'
    ];

    // Relasi ke Sales Order
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    // Relasi ke Item
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Relasi ke Batch (opsional)
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_no', 'batch_no');
    }
}
