<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\AiGlobalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GlobalWebController extends Controller
{
    protected AiGlobalService $globalService;

    public function __construct(AiGlobalService $globalService)
    {
        $this->globalService = $globalService;
    }

    public function index(): View
    {
        $business = Auth::user()->business;
        $products = $business ? $business->products()->latest()->get() : collect();

        return view('user.global.index', compact('products'));
    }

    public function translate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'target_language' => 'required|in:english,mandarin',
        ]);

        $product = Product::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($validated['product_id']);

        $result = $this->globalService->translateForExport(
            $product->name,
            $product->description ?? '',
            $validated['target_language'],
            Auth::id()
        );

        return redirect()->back()->with('translation', $result);
    }
}
