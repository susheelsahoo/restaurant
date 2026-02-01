<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WineCategory extends Model
{
    use HasFactory;

    protected $table = 'wine_categories';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];
}
