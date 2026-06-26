<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\AiStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiStockApiController extends Controller
{
    public function __construct(
        private AiStockService $aiStockService
    ) {}

    public function analyze(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        try {
            $products = Product::where('business_id', $business->id)->get();
            $productsData = $products->map(fn($p) => [
                'name' => $p->name,
                'stock' => $p->stock,
                'min_stock_alert' => $p->min_stock_alert,
            ])->toArray();
            $result = $this->aiStockService->analyzeStock($productsData, $request->user()->id);

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }

    public function movements(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $movements = StockMovement::where('business_id', $business->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json(['data' => $movements]);
    }

    public function storeMovement(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer',
            'reason' => 'nullable|string',
        ]);

        $product = Product::where('business_id', $business->id)
            ->where('id', $validated['product_id'])
            ->firstOrFail();

        $movement = StockMovement::create([
            'business_id' => $business->id,
            'product_id' => $validated['product_id'],
            'movement_type' => $validated['movement_type'],
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'] ?? null,
        ]);

        if ($validated['movement_type'] === 'in') {
            $product->increment('stock', $validated['quantity']);
        } elseif ($validated['movement_type'] === 'out') {
            $product->decrement('stock', $validated['quantity']);
        } elseif ($validated['movement_type'] === 'adjustment') {
            $product->update(['stock' => $validated['quantity']]);
        }

        return response()->json(['data' => $movement], 201);
    }
}
