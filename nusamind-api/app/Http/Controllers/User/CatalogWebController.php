<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\AiCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogWebController extends Controller
{
    protected AiCatalogService $catalogService;

    public function __construct(AiCatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function index(): View
    {
        $business = Auth::user()->business;
        $products = $business ? $business->products()->latest()->get() : collect();

        return view('user.catalog.index', compact('products'));
    }

    public function enhance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($validated['product_id']);

        $category = $product->business->category?->name;

        $result = $this->catalogService->enhanceProduct(
            $product->name,
            $product->description ?? '',
            $category,
            Auth::id()
        );

        session()->flash('enhance_result', $result);
        session()->flash('enhance_product_id', $product->id);

        return back();
    }

    public function apply($id): RedirectResponse
    {
        $product = Product::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $result = session('enhance_result');

        if (!$result) {
            return redirect()->route('user.catalog.index')->with('error', 'Tidak ada hasil optimasi yang tersimpan.');
        }

        $product->update([
            'name' => $result['optimized_name'],
            'description' => $result['optimized_description'],
        ]);

        session()->forget(['enhance_result', 'enhance_product_id']);

        return redirect()->route('user.catalog.index')->with('success', 'Produk berhasil dioptimasi!');
    }
}
