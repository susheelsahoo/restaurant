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
        'name',
        'phone',
        'email',
        'status'
    ];
}
