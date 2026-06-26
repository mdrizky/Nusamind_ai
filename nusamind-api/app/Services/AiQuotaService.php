<?php

namespace App\Services;

use App\Models\AiUsageLog;

class AiQuotaService
{
    public const DAILY_LIMIT = 30;

    public function checkAndIncrement(int $userId, string $feature, ?int $tokens = null): bool
    {
        $dailyUsage = AiUsageLog::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        if ($dailyUsage >= self::DAILY_LIMIT) {
            return false;
        }

        AiUsageLog::create([
            'user_id' => $userId,
            'feature' => $feature,
            'tokens_used' => $tokens,
            'status' => 'success',
        ]);

        return true;
    }

    public function check(int $userId): bool
    {
        $dailyUsage = AiUsageLog::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        return $dailyUsage < self::DAILY_LIMIT;
    }

    public function getRemaining(int $userId): int
    {
        $dailyUsage = AiUsageLog::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        return max(0, self::DAILY_LIMIT - $dailyUsage);
    }
}
