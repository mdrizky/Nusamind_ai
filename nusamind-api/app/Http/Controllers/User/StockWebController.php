<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\AiStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockWebController extends Controller
{
    public function __construct(protected AiStockService $aiStock) {}

    public function index(): View|RedirectResponse
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }
        $products = $business->products()->latest()->get();
        $statusCounts = ['aman' => 0, 'menipis' => 0, 'habis' => 0];
        foreach ($products as $product) {
            if ($product->stock <= 0) {
                $statusCounts['habis']++;
            } elseif ($product->stock <= $product->min_stock_alert) {
                $statusCounts['menipis']++;
            } else {
                $statusCounts['aman']++;
            }
        }
        return view('user.stock.index', compact('products', 'statusCounts'));
    }

    public function aiRecommend(): RedirectResponse
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }
        $products = $business->products()->latest()->get();
        $productsData = $products->map(fn($p) => [
            'name' => $p->name,
            'stock' => $p->stock,
            'min_stock_alert' => $p->min_stock_alert,
        ])->toArray();
        try {
            $result = $this->aiStock->analyzeStock($productsData, Auth::id());
            return redirect()->route('user.stock.index')->with('recommendations', $result);
        } catch (\Exception $e) {
            return redirect()->route('user.stock.index')->with('error', $e->getMessage());
        }
    }

    public function movements(): View|RedirectResponse
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }
        $movements = StockMovement::where('business_id', $business->id)
            ->with('product:id,name')
            ->latest()
            ->paginate(15);
        return view('user.stock.movements', compact('movements'));
    }

    public function adjustStock(Request $request): RedirectResponse
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'reason' => 'nullable|string',
        ]);
        $product = Product::where('business_id', $business->id)->findOrFail($validated['product_id']);
        StockMovement::create([
            'business_id' => $business->id,
            'product_id' => $validated['product_id'],
            'movement_type' => 'adjustment',
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'] ?? null,
        ]);
        $product->increment('stock', $validated['quantity']);
        return redirect()->route('user.stock.index')->with('success', 'Stok berhasil disesuaikan');
    }
}
