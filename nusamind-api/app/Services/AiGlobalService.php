<?php

namespace App\Services;

use App\Exceptions\AiParsingException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiGlobalService
{
    protected AiQuotaService $quotaService;

    public function __construct(AiQuotaService $quotaService)
    {
        $this->quotaService = $quotaService;
    }

    private function systemPrompt(): string
    {
        return 'Kamu adalah asisten ekspor UMKM Indonesia bernama NusamindGlobal. Tugasmu: bantu UMKM membuat deskripsi produk bahasa Inggris/Mandarin untuk pasar global.

Input: nama produk (Indonesia), deskripsi (Indonesia), target_language (english/mandarin)
Output: JSON {"translated_name": "...", "translated_description": "...", "export_pitch": "...", "target_market_hints": ["...", "..."]}

ATURAN WAJIB:
1. Output HARUS berupa JSON murni, tanpa teks tambahan, tanpa markdown, tanpa markdown code block.
2. Jangan gunakan ```json atau ``` di output.
3. Jangan beri komentar, penjelasan, atau teks pembuka/penutup apa pun.
4. Output hanya JSON, tidak boleh ada teks lain.';
    }

    public function translateForExport(string $productName, string $description, string $targetLanguage, int $userId): array
    {
        if (!$this->quotaService->checkAndIncrement($userId, 'global')) {
            throw new \Exception('Kamu sudah mencapai batas harian 30 kali penggunaan AI. Lanjutkan besok ya!');
        }

        $endpoint = config('services.ai.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
        $apiKey = config('services.ai.api_key');
        $model = config('services.ai.model', 'llama-3.3-70b-versatile');

        $input = 'Nama produk: ' . $productName . "\nDeskripsi: " . $description . "\nTarget bahasa: " . $targetLanguage;
        $input .= "\n\nOutput JSON format: {\"translated_name\": \"...\", \"translated_description\": \"...\", \"export_pitch\": \"...\", \"target_market_hints\": [\"...\", \"...\"]}";

        try {
            $response = Http::timeout(15)
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $input],
                    ],
                    'temperature' => 0.3,
                ]);

            $raw = $response->json('choices.0.message.content');

            if (!$raw) {
                throw new AiParsingException();
            }

            $clean = trim($raw);
            $clean = str_replace(['```json', '```', '`'], '', $clean);
            $decoded = json_decode($clean, true);

            if (!isset($decoded['translated_name'])) {
                throw new AiParsingException();
            }

            return $decoded;
        } catch (\Exception $e) {
            if ($e instanceof AiParsingException) {
                throw $e;
            }

            Log::error('AI Global error: ' . $e->getMessage());
            throw new \Exception('Maaf, Nusamind sedang sibuk. Coba lagi ya!');
        }
    }
}
