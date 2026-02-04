<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'booking_code',
        'visit_date',
        'visit_time',
        'guests',
        'customer_name',
        'phone',
        'email',
        'status'
    ];
}
