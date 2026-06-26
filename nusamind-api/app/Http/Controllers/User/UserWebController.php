<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessInsight;
use App\Models\Category;
use App\Models\ContentGeneration;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserWebController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        $business = $user->business;

        $transactionsQuery = Transaction::where('user_id', $user->id);
        $totalIncome = (clone $transactionsQuery)->where('type', 'pemasukan')->sum('amount');
        $totalExpense = (clone $transactionsQuery)->where('type', 'pengeluaran')->sum('amount');
        $transactionCount = (clone $transactionsQuery)->count();
        $recentTransactions = (clone $transactionsQuery)->latest()->take(5)->get();

        $productCount = $business ? $business->products()->count() : 0;
        $aiUsageCount = $user->aiUsageLogs()->count();
        $latestInsight = BusinessInsight::where('user_id', $user->id)->latest()->first();

        return view('user.dashboard', compact(
            'business', 'totalIncome', 'totalExpense', 'transactionCount',
            'recentTransactions', 'productCount', 'aiUsageCount', 'latestInsight'
        ));
    }

    public function business(): View
    {
        $business = Auth::user()->business;
        $categories = Category::all();
        return view('user.business', compact('business', 'categories'));
    }

    public function businessUpdate(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'business_name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'city' => 'required|string|max:100',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'business_name' => $validated['business_name'],
            'category_id' => $validated['category_id'],
            'city' => $validated['city'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $business = $user->business;

        if ($business) {
            $business->update($data);
        } else {
            $data['user_id'] = $user->id;
            Business::create($data);
        }

        return redirect()->route('user.business')->with('success', 'Profil usaha berhasil disimpan');
    }

    public function products(): View
    {
        $user = Auth::user();
        $business = $user->business;
        $products = $business ? $business->products()->latest()->get() : collect();
        return view('user.products.index', compact('products'));
    }

    public function productCreate(): View
    {
        $product = null;
        return view('user.products.form', compact('product'));
    }

    public function productStore(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $business->products()->create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'] ?? 0,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('user.products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function productEdit($id): View|RedirectResponse
    {
        $product = Product::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        return view('user.products.form', compact('product'));
    }

    public function productUpdate(Request $request, $id): RedirectResponse
    {
        $product = Product::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $product->update($validated);
        return redirect()->route('user.products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function productDestroy($id): RedirectResponse
    {
        $product = Product::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $product->delete();
        return redirect()->route('user.products.index')->with('success', 'Produk berhasil dihapus');
    }

    public function contentHistory(): View
    {
        $contents = ContentGeneration::where('user_id', Auth::id())
            ->with('product:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user.content.index', compact('contents'));
    }

    public function notifications(): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.notifications.index', compact('notifications'));
    }

    public function markNotificationRead($id): RedirectResponse
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    public function transactions(Request $request): View
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all');
        $period = $request->get('period', 'all');

        $query = Transaction::where('user_id', $user->id);

        if (in_array($filter, ['pemasukan', 'pengeluaran'])) {
            $query->where('type', $filter);
        }

        if ($period === 'today') {
            $query->whereDate('transaction_date', today());
        } elseif ($period === 'week') {
            $query->where('transaction_date', '>=', now()->startOfWeek());
        } elseif ($period === 'month') {
            $query->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year);
        }

        $transactions = $query->latest()->paginate(15);
        $totalIncome = Transaction::where('user_id', $user->id)->where('type', 'pemasukan')->sum('amount');
        $totalExpense = Transaction::where('user_id', $user->id)->where('type', 'pengeluaran')->sum('amount');

        return view('user.transactions.index', compact('transactions', 'filter', 'period', 'totalIncome', 'totalExpense'));
    }

    public function profile(): View
    {
        return view('user.profile');
    }

    public function features(): View
    {
        $user = Auth::user();
        $business = $user->business;

        $modules = [
            ['name' => 'NusaFinance', 'icon' => 'bi-calculator', 'color' => '#0F9D8E', 'desc' => 'Catat & analisis keuangan', 'route' => 'user.transactions'],
            ['name' => 'NusaMarketing', 'icon' => 'bi-megaphone', 'color' => '#F2B705', 'desc' => 'Buat konten promosi AI', 'route' => 'user.content.index'],
            ['name' => 'NusaInsight', 'icon' => 'bi-graph-up', 'color' => '#8B5CF6', 'desc' => 'Wawasan bisnis mingguan', 'route' => 'user.dashboard'],
            ['name' => 'NusaReply', 'icon' => 'bi-chat-dots', 'color' => '#06B6D4', 'desc' => 'Balas pelanggan dengan AI', 'route' => 'user.reply.index'],
            ['name' => 'NusaStock', 'icon' => 'bi-box-seam', 'color' => '#10B981', 'desc' => 'Monitor & restok barang', 'route' => 'user.stock.index'],
            ['name' => 'NusaCampaign', 'icon' => 'bi-bullseye', 'color' => '#F59E0B', 'desc' => 'Rencanakan promosi', 'route' => 'user.campaign.index'],
            ['name' => 'NusaLoyal', 'icon' => 'bi-people', 'color' => '#EC4899', 'desc' => 'Kelola pelanggan setia', 'route' => 'user.loyal.index'],
            ['name' => 'NusaPrice', 'icon' => 'bi-tags', 'color' => '#EF4444', 'desc' => 'Analisis harga optimal', 'route' => 'user.price.index'],
            ['name' => 'NusaCatalog', 'icon' => 'bi-collection', 'color' => '#6366F1', 'desc' => 'Optimasi katalog produk', 'route' => 'user.catalog.index'],
            ['name' => 'NusaGlobal', 'icon' => 'bi-globe2', 'color' => '#14B8A6', 'desc' => 'Ekspor ke pasar global', 'route' => 'user.global.index'],
            ['name' => 'NusaScore', 'icon' => 'bi-heart-pulse', 'color' => '#F43F5E', 'desc' => 'Cek skor kesehatan', 'route' => 'user.score.index'],
            ['name' => 'NusaCoach', 'icon' => 'bi-person-workspace', 'color' => '#7C3AED', 'desc' => 'Mentor bisnis AI', 'route' => 'user.coach.index'],
        ];

        return view('user.features', compact('modules'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.profile')->with('success', 'Password berhasil diubah');
    }
}
