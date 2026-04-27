<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'requests';

    protected $fillable = [
        'request_no',
        'user_id',
        'department_id',
        'priority',
        'status',
        'manager_comment',
        'admin_comment',
        'needed_by',
        'created_at',
    ];

    protected $casts = [
        'needed_by' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'items_count',
        'total_quantity',
        'total_price',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class, 'request_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'request_id');
    }

    public function getItemsCountAttribute(): int
    {
        return $this->relationLoaded('items')
            ? $this->items->count()
            : $this->items()->count();
    }

    public function getTotalQuantityAttribute(): float
    {
        $sum = $this->relationLoaded('items')
            ? $this->items->sum(fn (RequestItem $item) => (float) $item->quantity)
            : (float) $this->items()->sum('quantity');

        return (float) $sum;
    }

    public function getTotalPriceAttribute(): float
    {
        if ($this->relationLoaded('items')) {
            return (float) $this->items->sum(function (RequestItem $item) {
                $product = $item->relationLoaded('product')
                    ? $item->product
                    : $item->product()->first(['id', 'estimated_price']);

                return ((float) $item->quantity) * ((float) ($product?->estimated_price ?? 0));
            });
        }

        return (float) $this->items()
            ->leftJoin('products', 'products.id', '=', 'request_items.product_id')
            ->selectRaw('COALESCE(SUM(request_items.quantity * products.estimated_price), 0) as total_price')
            ->value('total_price');
    }
}
