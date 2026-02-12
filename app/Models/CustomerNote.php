<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerNote extends Model
{
    protected $fillable = [
        'customer_id',
        'note',
        'note_type',
        'created_by'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
