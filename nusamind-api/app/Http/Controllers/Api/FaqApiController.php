<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessFaq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $faqs = BusinessFaq::where('business_id', $business->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $faqs]);
    }

    public function store(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'required|string',
        ]);

        $faq = BusinessFaq::create([
            'business_id' => $business->id,
            ...$validated,
        ]);

        return response()->json(['data' => $faq], 201);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $faq = BusinessFaq::where('business_id', $business->id)->findOrFail($id);
        $faq->delete();

        return response()->json(['message' => 'OK']);
    }
}
