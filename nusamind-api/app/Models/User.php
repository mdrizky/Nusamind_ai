<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $attributes = [
        'role' => 'user',
        'status' => 'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function business()
    {
        return $this->hasOne(Business::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function contentGenerations()
    {
        return $this->hasMany(ContentGeneration::class);
    }

    public function businessInsights()
    {
        return $this->hasMany(BusinessInsight::class);
    }

    public function exportDescriptions()
    {
        return $this->hasMany(ExportDescription::class);
    }

    public function aiUsageLogs()
    {
        return $this->hasMany(AiUsageLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function contentReports()
    {
        return $this->hasMany(ContentReport::class, 'reported_by');
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
