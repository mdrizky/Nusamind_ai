<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\AiLoyalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoyalWebController extends Controller
{
    protected AiLoyalService $aiLoyal;

    public function __construct(AiLoyalService $aiLoyal)
    {
        $this->aiLoyal = $aiLoyal;
    }

    public function index(): View
    {
        $user = Auth::user();
        $business = $user->business;

        $customers = Customer::where('business_id', $business?->id)
            ->latest()
            ->paginate(15);

        $vipCount = Customer::where('business_id', $business?->id)
            ->where('segment', 'vip')
            ->count();
        $regularCount = Customer::where('business_id', $business?->id)
            ->where('segment', 'regular')
            ->count();
        $newCount = Customer::where('business_id', $business?->id)
            ->where('segment', 'new')
            ->count();

        return view('user.loyal.index', compact(
            'customers', 'vipCount', 'regularCount', 'newCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $business = Auth::user()->business;

        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }

        Customer::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('user.loyal.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $customer = Customer::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('user.loyal.index')->with('success', 'Data pelanggan berhasil diperbarui');
    }

    public function destroy($id): RedirectResponse
    {
        $customer = Customer::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $customer->delete();

        return redirect()->route('user.loyal.index')->with('success', 'Pelanggan berhasil dihapus');
    }

    public function generateFollowUp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        $customer = Customer::with('business')
            ->whereHas('business', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->findOrFail($validated['customer_id']);

        $customerData = [
            'nama_pelanggan' => $customer->name,
            'segment' => $customer->segment ?? 'new',
            'total_transaksi' => $customer->total_orders ?? 0,
            'total_belanja' => $customer->total_spent ?? 0,
            'tanggal_transaksi_terakhir' => $customer->last_order_date?->format('Y-m-d'),
        ];

        $followUp = $this->aiLoyal->generateFollowUp($customerData, Auth::id());

        return redirect()->route('user.loyal.index')
            ->with('success', 'Pesan follow-up berhasil dibuat!')
            ->with('follow_up', $followUp);
    }
}
