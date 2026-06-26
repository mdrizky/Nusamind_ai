<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignPlan extends Model
{
    use HasFactory;
    const UPDATED_AT = null;

    protected $fillable = [
        'business_id',
        'campaign_name',
        'campaign_goal',
        'target_product_id',
        'plan_result',
        'caption',
        'broadcast_message',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function targetProduct()
    {
        return $this->belongsTo(Product::class, 'target_product_id');
    }
}
