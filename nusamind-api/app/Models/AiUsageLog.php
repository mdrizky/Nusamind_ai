<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'feature',
        'tokens_used',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
