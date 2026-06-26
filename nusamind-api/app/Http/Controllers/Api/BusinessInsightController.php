<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessInsight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessInsightController extends Controller
{
    public function latest(Request $request): JsonResponse
    {
        $insight = BusinessInsight::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$insight) {
            return response()->json([
                'message' => 'Belum ada briefing mingguan. Yuk mulai catat jualanmu!',
                'insight' => null,
            ]);
        }

        return response()->json([
            'period_start' => $insight->period_start->format('Y-m-d'),
            'period_end' => $insight->period_end->format('Y-m-d'),
            'narrative_text' => $insight->narrative_text,
            'top_product' => $insight->top_product,
            'low_stock_product' => $insight->low_stock_product,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $insights = BusinessInsight::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'insights' => $insights,
        ]);
    }
}
