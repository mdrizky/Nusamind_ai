<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $customers = Customer::where('business_id', $business->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json(['data' => $customers]);
    }

    public function store(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create([
            'business_id' => $business->id,
            ...$validated,
        ]);

        return response()->json(['data' => $customer], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $customer = Customer::where('business_id', $business->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json(['data' => $customer->fresh()]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 422);
        }

        $customer = Customer::where('business_id', $business->id)->findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'OK']);
    }
}
