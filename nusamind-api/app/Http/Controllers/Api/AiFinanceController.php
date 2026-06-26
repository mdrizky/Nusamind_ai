<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Services\AiFinanceService;
use App\Services\AiQuotaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiFinanceController extends Controller
{
    public function __construct(
        private AiFinanceService $aiFinanceService,
        private AiQuotaService $aiQuotaService
    ) {}

    public function extract(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'input_text' => 'required|string|min:3',
        ]);

        $userId = $request->user()->id;

        if (!$this->aiQuotaService->check($userId)) {
            return response()->json([
                'message' => 'Kamu sudah mencapai batas pemakaian AI hari ini (30x). Besok lagi ya!',
            ], 429);
        }

        try {
            $transactions = $this->aiFinanceService->extractTransactions(
                $validated['input_text'],
                $userId
            );

            $this->aiQuotaService->checkAndIncrement($userId, 'finance');

            return response()->json([
                'transactions' => $transactions,
                'note' => 'Silakan konfirmasi sebelum disimpan',
            ]);
        } catch (\Exception $e) {
            AiUsageLog::create([
                'user_id' => $userId,
                'feature' => 'finance',
                'status' => 'timeout',
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], 503);
        }
    }
}
