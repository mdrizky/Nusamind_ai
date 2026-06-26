<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use App\Services\AiCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiCatalogApiController extends Controller
{
    public function __construct(
        private AiCatalogService $aiCatalogService
    ) {}

    public function enhance(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::where('business_id', $business->id)
            ->where('id', $validated['product_id'])
            ->firstOrFail();

        try {
            $category = $product->business?->category?->name;
            $result = $this->aiCatalogService->enhanceProduct(
                $product->name,
                $product->description ?? '',
                $category,
                $request->user()->id
            );

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}
