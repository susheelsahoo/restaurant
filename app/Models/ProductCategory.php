<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_budget',
        'status',
    ];

    protected $casts = [
        'monthly_budget' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProductCategory $productCategory) {
            if (empty($productCategory->slug)) {
                $productCategory->slug = Str::slug($productCategory->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'category_suppliers', 'category_id', 'supplier_id')
            ->orderBy('suppliers.name');
    }
}
