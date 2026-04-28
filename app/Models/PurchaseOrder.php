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
        'parent_po_id',
        'po_suffix',
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
        'category_summary',
        'total_amount',
    ];

    public function request()
    {
        return $this->belongsTo(PurchaseRequest::class, 'request_id');
    }

    public function parent()
    {
        return $this->belongsTo(PurchaseOrder::class, 'parent_po_id');
    }

    public function subPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'parent_po_id')
            ->orderBy('po_suffix')
            ->orderBy('id');
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
        $total = (float) $this->items->sum(function (PoItem $item) {
            return ((float) $item->quantity) * ((float) $item->unit_price);
        });

        if ($this->parent_po_id !== null) {
            return $total;
        }

        $subOrders = $this->relationLoaded('subPurchaseOrders')
            ? $this->subPurchaseOrders
            : $this->subPurchaseOrders()->with('items')->get();

        return $total + (float) $subOrders->sum(function (PurchaseOrder $purchaseOrder) {
            return $purchaseOrder->items->sum(function (PoItem $item) {
                return ((float) $item->quantity) * ((float) $item->unit_price);
            });
        });
    }

    public function getCategorySummaryAttribute(): string
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with('product.category:id,name')->get();

        if ($this->parent_po_id === null) {
            $subOrders = $this->relationLoaded('subPurchaseOrders')
                ? $this->subPurchaseOrders
                : $this->subPurchaseOrders()->with('items.product.category:id,name')->get();

            $items = $items->merge($subOrders->flatMap(fn (PurchaseOrder $purchaseOrder) => $purchaseOrder->items));
        }

        $categories = $items
            ->map(fn (PoItem $item) => $item->product?->category?->name ?: 'Uncategorized')
            ->filter()
            ->unique()
            ->values();

        return $categories->isNotEmpty()
            ? $categories->implode(', ')
            : 'Uncategorized';
    }

    public function isMainPo(): bool
    {
        return $this->parent_po_id === null;
    }

    public function refreshStatusFromSubOrders(): void
    {
        if ($this->parent_po_id !== null) {
            $this->parent?->refreshStatusFromSubOrders();
            return;
        }

        $statuses = $this->subPurchaseOrders()
            ->pluck('status')
            ->values();

        if ($statuses->isEmpty()) {
            return;
        }

        $status = match (true) {
            $statuses->contains('delayed') => 'delayed',
            $statuses->every(fn (string $value) => $value === 'completed') => 'completed',
            $statuses->contains('partial') => 'partial',
            $statuses->every(fn (string $value) => $value === 'confirmed') => 'confirmed',
            $statuses->every(fn (string $value) => in_array($value, ['sent', 'confirmed', 'partial', 'completed'], true)) => 'sent',
            default => 'draft',
        };

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }
}
