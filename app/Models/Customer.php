<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'date_of_anniversary',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_anniversary' => 'date',
        'is_active' => 'boolean'
    ];
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function notes()
    {
        return $this->hasMany(CustomerNote::class)->latest();
    }
}
