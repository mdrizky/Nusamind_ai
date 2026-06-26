<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use App\Services\AiPriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiPriceApiController extends Controller
{
    public function __construct(
        private AiPriceService $aiPriceService
    ) {}

    public function recommend(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'competitor_price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::where('business_id', $business->id)
            ->where('id', $validated['product_id'])
            ->firstOrFail();

        try {
            $result = $this->aiPriceService->recommendPrice(
                $product->name,
                (float) ($product->price ?? 0),
                $product->cost_estimate ? (float) $product->cost_estimate : null,
                $validated['competitor_price'] ? (float) $validated['competitor_price'] : null,
                $request->user()->id
            );

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}
