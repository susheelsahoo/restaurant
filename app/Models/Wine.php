<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wine extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'image',
        'wine_category_id',
        'is_active',
        'position',
    ];

    public function category()
    {
        return $this->belongsTo(WineCategory::class, 'wine_category_id');
    }
}
