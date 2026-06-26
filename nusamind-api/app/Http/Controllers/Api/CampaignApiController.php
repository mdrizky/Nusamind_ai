<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\CampaignPlan;
use App\Models\Product;
use App\Services\AiCampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignApiController extends Controller
{
    public function __construct(
        private AiCampaignService $aiCampaignService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $campaigns = CampaignPlan::where('business_id', $business->id)
            ->with('targetProduct:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $campaigns]);
    }

    public function generate(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $validated = $request->validate([
            'campaign_goal' => 'required|string',
            'target_product_id' => 'required|exists:products,id',
        ]);

        try {
            $productName = null;
            if ($validated['target_product_id']) {
                $product = Product::find($validated['target_product_id']);
                $productName = $product?->name;
            }
            $result = $this->aiCampaignService->generateCampaign(
                $validated['campaign_goal'],
                $productName,
                $request->user()->id
            );

            $plan = CampaignPlan::create([
                'business_id' => $business->id,
                'campaign_goal' => $validated['campaign_goal'],
                'target_product_id' => $validated['target_product_id'],
                'campaign_name' => $result['campaign_name'] ?? 'Campaign',
                'plan_result' => json_encode($result),
                'caption' => $result['caption'] ?? null,
                'broadcast_message' => $result['broadcast_message'] ?? null,
            ]);

            return response()->json(['data' => $plan], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $campaign = CampaignPlan::where('business_id', $business->id)->findOrFail($id);
        $campaign->delete();

        return response()->json(['message' => 'OK']);
    }
}
