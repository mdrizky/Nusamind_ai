<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function broadcast(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'body' => 'required|string',
        ]);

        $users = User::where('role', 'user')->where('status', 'active')->get();

        $count = 0;
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => $validated['title'],
                'body' => $validated['body'],
            ]);
            $count++;
        }

        return response()->json([
            'message' => "Notifikasi berhasil dikirim ke {$count} user",
            'sent_count' => $count,
        ]);
    }
}
