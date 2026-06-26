<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentReport extends Model
{
    use HasFactory;
    const UPDATED_AT = null;

    protected $fillable = [
        'content_generation_id',
        'reported_by',
        'reason',
        'status',
    ];

    public function contentGeneration()
    {
        return $this->belongsTo(ContentGeneration::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
