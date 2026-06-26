<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\AiLoyalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiLoyalApiController extends Controller
{
    public function __construct(
        private AiLoyalService $aiLoyalService
    ) {}

    public function generateFollowUp(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        try {
            $result = $this->aiLoyalService->generateFollowUp(
                $validated['customer_id'],
                $business->id
            );

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}
