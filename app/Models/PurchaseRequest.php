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
}
