<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\AiCoachService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiCoachApiController extends Controller
{
    public function __construct(
        private AiCoachService $aiCoachService
    ) {}

    public function chat(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $result = $this->aiCoachService->chat(
                $validated['message'],
                $business->id
            );

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}
