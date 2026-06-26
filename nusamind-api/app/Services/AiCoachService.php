<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCoachService
{
    protected AiQuotaService $quotaService;

    public function __construct(AiQuotaService $quotaService)
    {
        $this->quotaService = $quotaService;
    }

    private function systemPrompt(): string
    {
        return 'Kamu adalah mentor bisnis UMKM Indonesia bernama Coach Nusamind. Jawab pertanyaan UMKM tentang cara mengembangkan usaha, tips marketing, keuangan, dll. Gunakan bahasa Indonesia santai namun informatif. Beri saran praktis yang aplikatif untuk UMKM dengan budget terbatas.

Hindari penggunaan markdown berlebihan. Gunakan teks biasa untuk respons agar mudah dibaca di chat.';
    }

    public function chat(string $message, int $userId): array
    {
        if (!$this->quotaService->checkAndIncrement($userId, 'coach')) {
            throw new \Exception('Kamu sudah mencapai batas harian 30 kali penggunaan AI. Lanjutkan besok ya!');
        }

        $endpoint = config('services.ai.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
        $apiKey = config('services.ai.api_key');
        $model = config('services.ai.model', 'llama-3.3-70b-versatile');

        try {
            $response = Http::timeout(15)
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $message],
                    ],
                    'temperature' => 0.7,
                ]);

            $raw = $response->json('choices.0.message.content');

            if (!$raw) {
                throw new \Exception('Gagal mendapatkan respons dari AI');
            }

            $reply = trim($raw);

            return [
                'reply' => $reply,
                'suggestions' => [
                    'Coba tanya tentang tips marketing',
                    'Tanya cara mengelola keuangan usaha',
                ],
            ];
        } catch (\Exception $e) {
            Log::error('AI Coach error: ' . $e->getMessage());
            throw new \Exception('Maaf, Nusamind sedang sibuk. Coba lagi ya!');
        }
    }
}
