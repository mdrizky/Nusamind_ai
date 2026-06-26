<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Akses ditolak. Hanya admin yang bisa mengakses halaman ini.',
                ], 403);
            }
            return redirect('/login')->with('error', 'Akses ditolak');
        }

        if ($user->isSuspended()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Akun Anda telah dinonaktifkan, hubungi admin.',
                ], 403);
            }
            return redirect('/login')->with('error', 'Akun Anda telah dinonaktifkan');
        }

        return $next($request);
    }
}
