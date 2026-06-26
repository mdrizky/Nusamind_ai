<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (Business::where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'Kamu sudah punya profil usaha. Gunakan endpoint PUT /business/me untuk update.',
            ], 409);
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'city' => 'required|string|max:100',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string|max:255',
        ]);

        $business = Business::create([
            'user_id' => $request->user()->id,
            'business_name' => $validated['business_name'],
            'category_id' => $validated['category_id'],
            'city' => $validated['city'],
            'description' => $validated['description'] ?? null,
            'logo_path' => $validated['logo_path'] ?? null,
        ]);

        return response()->json([
            'message' => 'Profil usaha berhasil dibuat',
            'business' => $business->load('category'),
        ], 201);
    }

    public function show(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)
            ->with('category')
            ->first();

        if (!$business) {
            return response()->json([
                'message' => 'Profil usaha belum dibuat',
                'business' => null,
            ]);
        }

        return response()->json([
            'business' => $business,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $business = Business::where('user_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'business_name' => 'sometimes|string|max:150',
            'category_id' => 'sometimes|exists:categories,id',
            'city' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string|max:255',
        ]);

        $business->update($validated);

        return response()->json([
            'message' => 'Profil usaha berhasil diperbarui',
            'business' => $business->fresh()->load('category'),
        ]);
    }
}
