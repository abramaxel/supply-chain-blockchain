<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number', 'order_date', 'supplier', 'status'
    ];

    // Relasi: Satu PO punya banyak item
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    
}
