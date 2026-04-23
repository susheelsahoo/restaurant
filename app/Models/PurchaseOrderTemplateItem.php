<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderTemplateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'product_id',
        'item_name',
        'category_name',
        'default_quantity',
        'unit',
        'note',
        'sort_order',
    ];

    protected $casts = [
        'default_quantity' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function template()
    {
        return $this->belongsTo(PurchaseOrderTemplate::class, 'template_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
