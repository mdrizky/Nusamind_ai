<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with('business.category');

        if ($request->status && in_array($request->status, ['active', 'suspended'])) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($users);
    }

    public function suspend($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'suspended']);

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Akun user berhasil dinonaktifkan',
        ]);
    }

    public function activate($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        return response()->json([
            'message' => 'Akun user berhasil diaktifkan kembali',
        ]);
    }
}
