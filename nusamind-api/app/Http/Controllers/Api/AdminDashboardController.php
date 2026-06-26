<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')
            ->where('status', 'active')
            ->whereHas('transactions', function ($q) {
                $q->where('created_at', '>=', now()->subDays(7));
            })
            ->count();

        $totalTransactions = Transaction::count();

        $aiUsageToday = AiUsageLog::whereDate('created_at', today())->count();

        $aiUsagePerFeature = AiUsageLog::whereDate('created_at', today())
            ->selectRaw('feature, COUNT(*) as total')
            ->groupBy('feature')
            ->pluck('total', 'feature');

        return response()->json([
            'total_users' => $totalUsers,
            'active_users_7days' => $activeUsers,
            'total_transactions' => $totalTransactions,
            'ai_usage_today' => $aiUsageToday,
            'ai_usage_per_feature' => $aiUsagePerFeature,
        ]);
    }
}
