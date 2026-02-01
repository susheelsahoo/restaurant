<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $table = 'leads';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'source',
        'hotel_size',
        'notes',
        'status'
    ];
}
