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
        'is_deleted',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopePublished($query)
    {
        return $query->notDeleted()->where('is_published', true);
    }

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
