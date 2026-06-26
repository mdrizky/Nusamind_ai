<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_id',
        'name',
        'price',
        'stock',
        'image_path',
        'description',
        'cost_estimate',
        'min_stock_alert',
        'unit',
        'image_url',
        'is_active',
        'tags',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function contentGenerations()
    {
        return $this->hasMany(ContentGeneration::class);
    }

    public function exportDescriptions()
    {
        return $this->hasMany(ExportDescription::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
