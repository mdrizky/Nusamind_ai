<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
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

        return view('admin.users.index', compact('users'));
    }

    public function suspend($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'suspended']);
        $user->tokens()->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun user berhasil dinonaktifkan');
    }

    public function activate($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        return redirect()->route('admin.users.index')->with('success', 'Akun user berhasil diaktifkan kembali');
    }
}
