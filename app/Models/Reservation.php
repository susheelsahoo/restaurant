<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
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
