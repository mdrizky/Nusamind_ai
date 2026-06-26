<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\CustomerReply;
use App\Services\AiReplyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiReplyApiController extends Controller
{
    public function __construct(
        private AiReplyService $aiReplyService
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_message' => 'required|string',
            'intent' => 'nullable|string',
            'tone' => 'nullable|string',
        ]);

        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        try {
            $result = $this->aiReplyService->generateReply(
                $validated['customer_message'],
                $validated['intent'] ?? null,
                $validated['tone'] ?? null,
                $request->user()->id
            );

            CustomerReply::create([
                'business_id' => $business->id,
                'customer_message' => $validated['customer_message'],
                'intent' => $validated['intent'] ?? null,
                'tone' => $validated['tone'] ?? null,
                'generated_reply' => $result['reply'],
            ]);

            return response()->json([
                'data' => [
                    'reply' => $result['reply'],
                    'intent_detected' => $result['intent_detected'] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $replies = CustomerReply::where('business_id', $business->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json(['data' => $replies]);
    }

    public function save(Request $request, $id): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $reply = CustomerReply::where('business_id', $business->id)->findOrFail($id);
        $reply->update(['is_saved' => !$reply->is_saved]);

        return response()->json(['message' => 'OK']);
    }
}
