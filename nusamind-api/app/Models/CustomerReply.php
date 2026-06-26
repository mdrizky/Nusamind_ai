<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReply extends Model
{
    use HasFactory;
    const UPDATED_AT = null;

    protected $fillable = [
        'business_id',
        'customer_message',
        'intent',
        'tone',
        'generated_reply',
        'is_saved',
    ];

    protected $casts = [
        'is_saved' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
