<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AiCoachService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CoachWebController extends Controller
{
    protected AiCoachService $coachService;

    public function __construct(AiCoachService $coachService)
    {
        $this->coachService = $coachService;
    }

    public function index(): View
    {
        $messages = session('coach_messages', []);
        return view('user.coach.index', compact('messages'));
    }

    public function chat(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        session()->push('coach_messages', ['role' => 'user', 'content' => $validated['message']]);

        try {
            $result = $this->coachService->chat($validated['message'], $user->id);
            session()->push('coach_messages', ['role' => 'assistant', 'content' => $result['reply']]);
        } catch (\Exception $e) {
            session()->push('coach_messages', ['role' => 'assistant', 'content' => $e->getMessage()]);
        }

        $messages = session('coach_messages', []);
        if (count($messages) > 20) {
            session(['coach_messages' => array_slice($messages, -20)]);
        }

        return redirect()->back();
    }

    public function clear(): RedirectResponse
    {
        session()->forget('coach_messages');
        return redirect()->route('user.coach.index');
    }
}
