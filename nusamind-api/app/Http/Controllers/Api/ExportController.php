<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\ExportDescription;
use App\Models\Product;
use App\Services\AiQuotaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExportController extends Controller
{
    public function __construct(
        private AiQuotaService $aiQuotaService
    ) {}

    public function translate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'target_language' => 'required|in:en,zh',
        ]);

        $userId = $request->user()->id;
        $product = Product::findOrFail($validated['product_id']);

        if (!$this->aiQuotaService->check($userId)) {
            return response()->json([
                'message' => 'Batas pemakaian AI hari ini sudah habis (30x). Besok lagi ya!',
            ], 429);
        }

        $originalText = $product->description ?? $product->name;

        $langName = $validated['target_language'] === 'en' ? 'Inggris' : 'Mandarin';

        $systemPrompt = "Kamu adalah penerjemah profesional untuk deskripsi produk UMKM Indonesia yang akan
dipasarkan ke pasar internasional, bernama Nusamind.

ATURAN WAJIB:
1. Terjemahkan teks ke bahasa target ({$validated['target_language']}: en=Inggris, zh=Mandarin)
   dengan gaya marketing yang natural, BUKAN terjemahan kata-per-kata yang kaku.
2. Sertakan istilah kunci yang umum dicari di marketplace internasional (mis. \"handmade\",
   \"natural ingredients\") jika relevan dan sesuai fakta dari teks asli.
3. JANGAN menambahkan klaim yang tidak ada di teks asli (misal klaim \"organic\" jika
   tidak disebutkan sumber aslinya).
4. Output JSON murni: {\"translated_text\":\"...\"}";

        try {
            $response = Http::timeout(15)
                ->withToken(config('services.ai.api_key'))
                ->post(config('services.ai.endpoint', 'https://api.groq.com/openai/v1/chat/completions'), [
                    'model' => config('services.ai.model', 'llama-3.3-70b-versatile'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => "Teks asli: \"{$originalText}\"\nBahasa target: {$validated['target_language']}"],
                    ],
                    'temperature' => 0.3,
                ]);

            $raw = $response->json('choices.0.message.content');
            $clean = trim(str_replace(['```json', '```', '`'], '', $raw ?? ''));
            $decoded = json_decode($clean, true);

            $translatedText = $decoded['translated_text'] ?? $originalText;

            if ($decoded) {
                ExportDescription::create([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'target_language' => $validated['target_language'],
                    'original_text' => $originalText,
                    'translated_text' => $translatedText,
                ]);
            }

            $this->aiQuotaService->checkAndIncrement($userId, 'export', $response->json('usage.total_tokens'));

            return response()->json([
                'original_text' => $originalText,
                'translated_text' => $translatedText,
                'target_language' => $validated['target_language'],
            ]);
        } catch (\Exception $e) {
            Log::error('AI Export error: ' . $e->getMessage());

            AiUsageLog::create([
                'user_id' => $userId,
                'feature' => 'export',
                'status' => 'timeout',
            ]);

            return response()->json([
                'message' => 'Maaf, Nusamind belum bisa menerjemahkan sekarang. Coba lagi ya!',
            ], 503);
        }
    }
}
