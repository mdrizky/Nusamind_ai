<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function getUserBusiness(Request $request): Business
    {
        return Business::where('user_id', $request->user()->id)->firstOrFail();
    }

    public function index(Request $request): JsonResponse
    {
        $business = $this->getUserBusiness($request);

        $products = Product::where('business_id', $business->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['products' => $products]);
    }

    public function store(Request $request): JsonResponse
    {
        $business = $this->getUserBusiness($request);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'price' => 'required|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'image_path' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $product = Product::create([
            'business_id' => $business->id,
            ...$validated,
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'product' => $product,
        ], 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $business = $this->getUserBusiness($request);
        $product = Product::where('business_id', $business->id)->findOrFail($id);

        return response()->json(['product' => $product]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $business = $this->getUserBusiness($request);
        $product = Product::where('business_id', $business->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:150',
            'price' => 'sometimes|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'image_path' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Produk berhasil diperbarui',
            'product' => $product->fresh(),
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $business = $this->getUserBusiness($request);
        $product = Product::where('business_id', $business->id)->findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus',
        ]);
    }
}
