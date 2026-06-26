<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessInsight extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'narrative_text',
        'top_product',
        'low_stock_product',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
