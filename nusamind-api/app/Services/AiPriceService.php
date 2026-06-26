<?php

namespace App\Services;

use App\Exceptions\AiParsingException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiPriceService
{
    protected AiQuotaService $quotaService;

    public function __construct(AiQuotaService $quotaService)
    {
        $this->quotaService = $quotaService;
    }

    private function systemPrompt(): string
    {
        return 'Kamu adalah asisten penetapan harga UMKM Indonesia bernama NusamindPrice. Tugasmu: analisis harga produk dan beri rekomendasi harga optimal.

Input: nama produk, harga saat ini, estimasi modal (HPP), harga kompetitor (opsional)
Output: JSON {"product_name": "...", "current_price": 0, "cost_price": 0, "margin": 0, "recommended_price": 0, "min_price": 0, "max_price": 0, "reasoning": "..."}

ATURAN WAJIB:
1. Output HARUS berupa JSON murni, tanpa teks tambahan, tanpa markdown, tanpa markdown code block.
2. Jangan gunakan ```json atau ``` di output.
3. Jangan beri komentar, penjelasan, atau teks pembuka/penutup apa pun.
4. Output hanya JSON, tidak boleh ada teks lain.';
    }

    public function recommendPrice(string $productName, float $currentPrice, ?float $costPrice, ?float $competitorPrice, int $userId): array
    {
        if (!$this->quotaService->checkAndIncrement($userId, 'price')) {
            throw new \Exception('Kamu sudah mencapai batas harian 30 kali penggunaan AI. Lanjutkan besok ya!');
        }

        $endpoint = config('services.ai.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
        $apiKey = config('services.ai.api_key');
        $model = config('services.ai.model', 'llama-3.3-70b-versatile');

        $input = [
            'product_name' => $productName,
            'current_price' => $currentPrice,
            'cost_price' => $costPrice,
            'competitor_price' => $competitorPrice,
        ];

        try {
            $response = Http::timeout(15)
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => json_encode($input)],
                    ],
                    'temperature' => 0.2,
                ]);

            $raw = $response->json('choices.0.message.content');

            if (!$raw) {
                throw new AiParsingException();
            }

            $clean = trim($raw);
            $clean = str_replace(['```json', '```', '`'], '', $clean);
            $decoded = json_decode($clean, true);

            if (!isset($decoded['recommended_price'])) {
                throw new AiParsingException();
            }

            return $decoded;
        } catch (\Exception $e) {
            if ($e instanceof AiParsingException) {
                throw $e;
            }

            Log::error('AI Price error: ' . $e->getMessage());
            throw new \Exception('Maaf, Nusamind sedang sibuk. Coba lagi ya!');
        }
    }
}
