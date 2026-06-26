<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiUsageLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AiUsageLog::with('user:id,name,email');

        if ($request->feature && in_array($request->feature, ['finance', 'content', 'briefing', 'export'])) {
            $query->where('feature', $request->feature);
        }

        if ($request->status && in_array($request->status, ['success', 'failed', 'timeout'])) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.ai-usage.index', compact('logs'));
    }
}
