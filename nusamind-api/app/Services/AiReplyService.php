<?php

namespace App\Services;

use App\Exceptions\AiParsingException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiReplyService
{
    protected AiQuotaService $quotaService;

    public function __construct(AiQuotaService $quotaService)
    {
        $this->quotaService = $quotaService;
    }

    private function systemPrompt(): string
    {
        return 'Kamu adalah asisten balas chat UMKM Indonesia bernama NusamindReply. Tugasmu: bantu UMKM membalas pesan pelanggan dengan nada yang tepat.

Input: pesan pelanggan + intent (opsional) + tone (opsional)
Output: JSON dengan format {"reply": "...", "intent_detected": "..."}

ATURAN WAJIB:
1. Output HARUS berupa JSON murni, tanpa teks tambahan, tanpa markdown, tanpa markdown code block.
2. Jangan gunakan ```json atau ``` di output.
3. Jangan beri komentar, penjelasan, atau teks pembuka/penutup apa pun.
4. Output hanya JSON, tidak boleh ada teks lain.';
    }

    public function generateReply(string $customerMessage, ?string $intent, ?string $tone, int $userId): array
    {
        if (!$this->quotaService->checkAndIncrement($userId, 'reply')) {
            throw new \Exception('Kamu sudah mencapai batas harian 30 kali penggunaan AI. Lanjutkan besok ya!');
        }

        $endpoint = config('services.ai.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
        $apiKey = config('services.ai.api_key');
        $model = config('services.ai.model', 'llama-3.3-70b-versatile');

        $input = 'Pesan pelanggan: ' . $customerMessage;
        if ($intent) {
            $input .= "\nIntent: " . $intent;
        }
        if ($tone) {
            $input .= "\nTone: " . $tone;
        }
        $input .= "\n\nBalas dengan JSON format: {\"reply\": \"...\", \"intent_detected\": \"...\"}";

        try {
            $response = Http::timeout(15)
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $input],
                    ],
                    'temperature' => 0.4,
                ]);

            $raw = $response->json('choices.0.message.content');

            if (!$raw) {
                throw new AiParsingException();
            }

            $clean = trim($raw);
            $clean = str_replace(['```json', '```', '`'], '', $clean);
            $decoded = json_decode($clean, true);

            if (!isset($decoded['reply'])) {
                throw new AiParsingException();
            }

            return $decoded;
        } catch (\Exception $e) {
            if ($e instanceof AiParsingException) {
                throw $e;
            }

            Log::error('AI Reply error: ' . $e->getMessage());
            throw new \Exception('Maaf, Nusamind sedang sibuk. Coba lagi ya!');
        }
    }
}
