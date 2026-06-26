<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category_id',
        'business_name',
        'city',
        'description',
        'logo_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function faqs()
    {
        return $this->hasMany(BusinessFaq::class);
    }

    public function customerReplies()
    {
        return $this->hasMany(CustomerReply::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function campaignPlans()
    {
        return $this->hasMany(CampaignPlan::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function healthScores()
    {
        return $this->hasMany(HealthScore::class);
    }
}
