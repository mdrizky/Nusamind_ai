<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
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
            ->select('feature', DB::raw('count(*) as total'))
            ->groupBy('feature')
            ->pluck('total', 'feature')
            ->toArray();

        $userGrowthWeekly = User::where('role', 'user')
            ->where('created_at', '>=', now()->subWeeks(8))
            ->get()
            ->groupBy(fn($u) => $u->created_at->format('Y-W'))
            ->map(fn($group) => $group->count())
            ->toArray();

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'totalTransactions', 'aiUsageToday',
            'aiUsagePerFeature', 'userGrowthWeekly'
        ));
    }
}
