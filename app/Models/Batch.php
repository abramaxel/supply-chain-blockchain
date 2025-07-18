<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $primaryKey = 'batch_no';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'batch_no', 'item_id', 'location', 'expiry_date', 'quantity', 'batch_hash'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'batch_no', 'batch_no');
    }

    public function salesOrderItems()
    {
        return $this->hasMany(SalesOrderItem::class, 'batch_no', 'batch_no');
    }
}
