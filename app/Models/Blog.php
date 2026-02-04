<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'content',
        'image',
        'is_published',
        'meta_title',
        'meta_description',
    ];

    // If you have tags as a pivot relation:
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
