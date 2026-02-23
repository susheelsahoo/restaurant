<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'subject',
        'short_text',
        'message',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get email template by slug
     */
    public static function getBySlug($slug)
    {
        return self::where('slug', $slug)->where('is_active', true)->first();
    }

    /**
     * Get all reservation status email templates
     */
    public static function getReservationTemplates()
    {
        return self::whereIn('slug', [
            'reservation-pending',
            'reservation-confirmed',
            'reservation-canceled',
            'reservation-declined',
            'reservation-in-house',
            'reservation-complete',
        ])->get()->keyBy('slug');
    }
}
