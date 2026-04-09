<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'po_items';

    protected $fillable = [
        'po_id',
        'product_id',
        'quantity',
        'received_qty',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'received_qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }
}
