<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\AiPriceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PriceWebController extends Controller
{
    protected AiPriceService $priceService;

    public function __construct(AiPriceService $priceService)
    {
        $this->priceService = $priceService;
    }

    public function index(): View
    {
        $business = Auth::user()->business;
        $products = $business ? $business->products()->select('id', 'name', 'price', 'cost_estimate')->get() : collect();

        return view('user.price.index', compact('products'));
    }

    public function recommend(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'competitor_price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($validated['product_id']);

        $result = $this->priceService->recommendPrice(
            $product->name,
            (float) $product->price,
            $product->cost_estimate ? (float) $product->cost_estimate : null,
            $validated['competitor_price'] ? (float) $validated['competitor_price'] : null,
            Auth::id()
        );

        return redirect()->back()->with('price_result', $result);
    }
}
