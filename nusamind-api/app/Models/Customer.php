<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'address',
        'notes',
        'total_orders',
        'total_spent',
        'last_order_date',
        'segment',
    ];

    protected $casts = [
        'total_orders' => 'integer',
        'total_spent' => 'decimal:2',
        'last_order_date' => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
