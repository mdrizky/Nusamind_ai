<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentReportController extends Controller
{
    public function index(Request $request): View
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

        return view('admin.content-reports.index', compact('reports'));
    }

    public function resolve(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:reviewed,removed',
        ]);

        $report = ContentReport::findOrFail($id);
        $report->update(['status' => $validated['status']]);

        if ($validated['status'] === 'removed' && $report->contentGeneration) {
            $report->contentGeneration->delete();
        }

        return redirect()->route('admin.content-reports.index')
            ->with('success', 'Laporan berhasil diselesaikan');
    }
}
