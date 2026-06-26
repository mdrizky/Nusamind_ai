<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BusinessFaq;
use App\Models\CustomerReply;
use App\Services\AiReplyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReplyWebController extends Controller
{
    public function __construct(protected AiReplyService $aiReply) {}

    public function index(): View|RedirectResponse
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }
        $replies = CustomerReply::where('business_id', $business->id)->latest()->paginate(10);
        return view('user.reply.index', compact('replies'));
    }

    public function faqIndex(): View|RedirectResponse
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }
        $faqs = BusinessFaq::where('business_id', $business->id)->latest()->paginate(10);
        return view('user.reply.faq', compact('faqs'));
    }

    public function faqStore(Request $request): RedirectResponse
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:50',
        ]);
        BusinessFaq::create([
            'business_id' => $business->id,
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'category' => $validated['category'] ?? null,
        ]);
        return redirect()->route('user.reply.faq')->with('success', 'FAQ berhasil ditambahkan');
    }

    public function faqDestroy($id): RedirectResponse
    {
        $faq = BusinessFaq::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);
        $faq->delete();
        return redirect()->route('user.reply.faq')->with('success', 'FAQ berhasil dihapus');
    }

    public function generateReply(Request $request): RedirectResponse
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }
        $validated = $request->validate([
            'customer_message' => 'required|string',
            'intent' => 'nullable|string',
            'tone' => 'nullable|string',
        ]);
        try {
            $result = $this->aiReply->generateReply(
                $validated['customer_message'],
                $validated['intent'] ?? null,
                $validated['tone'] ?? null,
                Auth::id()
            );
            CustomerReply::create([
                'business_id' => $business->id,
                'customer_message' => $validated['customer_message'],
                'intent' => $validated['intent'] ?? null,
                'tone' => $validated['tone'] ?? null,
                'generated_reply' => $result['reply'],
            ]);
            return redirect()->route('user.reply.index')->with('success', 'Balasan berhasil dibuat')->with('reply', $result['reply']);
        } catch (\Exception $e) {
            return redirect()->route('user.reply.index')->with('error', $e->getMessage());
        }
    }

    public function history(): View|RedirectResponse
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('user.business')->with('error', 'Lengkapi profil usaha terlebih dahulu');
        }
        $replies = CustomerReply::where('business_id', $business->id)->where('is_saved', true)->latest()->paginate(10);
        return view('user.reply.history', compact('replies'));
    }

    public function saveReply($id): RedirectResponse
    {
        $reply = CustomerReply::whereHas('business', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);
        $reply->update(['is_saved' => !$reply->is_saved]);
        return back()->with('success', 'Status simpan berhasil diubah');
    }
}
