<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminContentReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ContentReport::with([
            'contentGeneration:id,caption_result,image_path,user_id',
            'contentGeneration.user:id,name',
            'reporter:id,name',
        ]);

        if ($request->status && in_array($request->status, ['pending', 'reviewed', 'removed'])) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($reports);
    }

    public function resolve(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:reviewed,removed',
        ]);

        $report = ContentReport::findOrFail($id);
        $report->update(['status' => $validated['status']]);

        if ($validated['status'] === 'removed' && $report->contentGeneration) {
            $report->contentGeneration->delete();
        }

        return response()->json([
            'message' => 'Laporan berhasil diselesaikan',
        ]);
    }
}
