<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    // Allow mass assignment for specified attributes
    protected $fillable = ['name', 'email', 'subject', 'message', 'is_read'];
}
