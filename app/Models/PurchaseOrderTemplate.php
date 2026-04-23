<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'priority',
        'status',
        'description',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderTemplateItem::class, 'template_id')->orderBy('sort_order')->orderBy('id');
    }
}
