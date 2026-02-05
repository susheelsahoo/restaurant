<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    const STATUS_NEW       = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_DECLINED  = 'declined';
    const STATUS_COMPLETE  = 'complete';

    protected $fillable = [
        'booking_code',
        'customer_name',
        'visit_date',
        'visit_time',
        'phone',
        'email',
        'guests',
        'status',
        'notes',
    ];
}
