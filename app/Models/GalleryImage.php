<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'image_width',
        'image_height',
        'home_display',
        'gallery_display',
        'is_active',
    ];
}
