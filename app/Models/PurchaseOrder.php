<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'po_number',
        'request_id',
        'supplier_id',
        'buyer_id',
        'status',
        'order_date',
        'expected_delivery',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery' => 'date',
    ];

    protected $appends = [
        'total_amount',
    ];

    public function request()
    {
        return $this->belongsTo(PurchaseRequest::class, 'request_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function items()
    {
        return $this->hasMany(PoItem::class, 'po_id');
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->items->sum(function (PoItem $item) {
            return ((float) $item->quantity) * ((float) $item->unit_price);
        });
    }
}
