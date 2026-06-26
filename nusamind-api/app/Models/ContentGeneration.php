<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentGeneration extends Model
{
    use HasFactory;
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'product_id',
        'image_path',
        'style',
        'caption_result',
        'hashtags_result',
        'whatsapp_template_result',
    ];

    protected function casts(): array
    {
        return [
            'hashtags_result' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function contentReports()
    {
        return $this->hasMany(ContentReport::class);
    }
}
