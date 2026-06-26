<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthScore extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'business_id',
        'total_score',
        'financial_score',
        'marketing_score',
        'sales_score',
        'customer_score',
        'stock_score',
        'breakdown_text',
        'recommendations',
        'scored_at',
    ];

    protected $casts = [
        'recommendations' => 'array',
        'scored_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
