<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAiUsageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AiUsageLog::with('user:id,name,email');

        if ($request->feature && in_array($request->feature, ['finance', 'content', 'briefing', 'export'])) {
            $query->where('feature', $request->feature);
        }

        if ($request->status && in_array($request->status, ['success', 'failed', 'timeout'])) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($logs);
    }
}
