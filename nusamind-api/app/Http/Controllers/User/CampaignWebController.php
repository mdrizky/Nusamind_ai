<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CampaignPlan;
use App\Services\AiCampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CampaignWebController extends Controller
{
    protected AiCampaignService $aiCampaign;

    public function __construct(AiCampaignService $aiCampaign)
    {
        $this->aiCampaign = $aiCampaign;
    }

    public function index(): View
    {
        $user = Auth::user();
        $business = $user->business;
        $campaigns = CampaignPlan::where('business_id', $business?->id)
            ->latest()
            ->paginate(10);
        $products = $business ? $business->products()->latest()->get() : collect();

        return view('user.campaign.index', compact('campaigns', 'products'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'campaign_goal' => 'required|string',
            'target_product_id' => 'nullable|exists:products,id',
        ]);

        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }

        $productName = null;
        if ($validated['target_product_id']) {
            $product = $business->products()->find($validated['target_product_id']);
            $productName = $product?->name;
        }

        $plan = $this->aiCampaign->generateCampaign(
            $validated['campaign_goal'],
            $productName,
            $user->id
        );

        $campaign = CampaignPlan::create([
            'business_id' => $business->id,
            'campaign_name' => $plan['campaign_name'] ?? 'Campaign',
            'campaign_goal' => $validated['campaign_goal'],
            'target_product_id' => $validated['target_product_id'],
            'caption' => $plan['caption'] ?? null,
            'broadcast_message' => $plan['broadcast_message'] ?? null,
            'plan_result' => json_encode($plan),
            'is_active' => true,
        ]);

        return redirect()->route('user.campaign.index')
            ->with('success', 'Rencana campaign berhasil dibuat!')
            ->with('plan', $plan);
    }

    public function activate($id): RedirectResponse
    {
        $campaign = CampaignPlan::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $campaign->update(['is_active' => !$campaign->is_active]);

        return back()->with('success', 'Status campaign berhasil diubah');
    }

    public function delete($id): RedirectResponse
    {
        $campaign = CampaignPlan::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $campaign->delete();

        return back()->with('success', 'Campaign berhasil dihapus');
    }
}
