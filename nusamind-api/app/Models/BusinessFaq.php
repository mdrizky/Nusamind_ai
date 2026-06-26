<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessFaq extends Model
{
    use HasFactory;
    const UPDATED_AT = null;

    protected $fillable = [
        'business_id',
        'question',
        'answer',
        'category',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
