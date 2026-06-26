<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isSuspended()) {
            return response()->json([
                'message' => 'Akun Anda telah dinonaktifkan, hubungi admin',
            ], 403);
        }

        return $next($request);
    }
}
