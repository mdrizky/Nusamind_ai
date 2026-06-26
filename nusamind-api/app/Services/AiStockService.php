<?php

namespace App\Services;

use App\Exceptions\AiParsingException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiStockService
{
    protected AiQuotaService $quotaService;

    public function __construct(AiQuotaService $quotaService)
    {
        $this->quotaService = $quotaService;
    }

    private function systemPrompt(): string
    {
        return 'Kamu adalah asisten manajemen stok UMKM Indonesia bernama NusamindStock. Tugasmu: analisis data stok produk dan beri rekomendasi.

Input: array produk dengan nama, stok saat ini, min_stok_alert, penjualan 7 hari terakhir
Output: JSON array [{"product_name": "...", "status": "aman/menipis/habis", "current_stock": 0, "recommended_restock": 0, "reason": "..."}]

ATURAN WAJIB:
1. Output HARUS berupa JSON murni, tanpa teks tambahan, tanpa markdown, tanpa markdown code block.
2. Jangan gunakan ```json atau ``` di output.
3. Jangan beri komentar, penjelasan, atau teks pembuka/penutup apa pun.
4. Output hanya JSON, tidak boleh ada teks lain.';
    }

    public function analyzeStock(array $productsData, int $userId): array
    {
        if (!$this->quotaService->checkAndIncrement($userId, 'stock')) {
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
                        ['role' => 'user', 'content' => json_encode($productsData)],
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

            if (!is_array($decoded)) {
                throw new AiParsingException();
            }

            return $decoded;
        } catch (\Exception $e) {
            if ($e instanceof AiParsingException) {
                throw $e;
            }

            Log::error('AI Stock error: ' . $e->getMessage());
            throw new \Exception('Maaf, Nusamind sedang sibuk. Coba lagi ya!');
        }
    }
}
