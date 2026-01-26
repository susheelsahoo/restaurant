<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_path',
        // 'expiry_date',
        'is_active',
    ];
    protected $attributes = [
        'is_active' => true,
    ];
    protected $casts = [
        'is_active' => 'boolean',
        // 'expiry_date' => 'date',
    ];
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
