<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'requests';

    protected $fillable = [
        'request_no',
        'user_id',
        'department_id',
        'priority',
        'status',
        'needed_by',
        'created_at',
    ];

    protected $casts = [
        'needed_by' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
