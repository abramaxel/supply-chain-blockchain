<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'item_id', 'batch_no', 'quantity', 'unit_price', 'total_price'
    ];

    // Relasi ke PO
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Relasi ke Item
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Relasi ke Batch (opsional jika batch_no tidak selalu terisi)
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_no', 'batch_no');
    }

    
}
