<?php

namespace App\Services;

use App\Exceptions\AiParsingException;
use App\Models\AiUsageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiFinanceService
{
    private function systemPrompt(): string
    {
        return 'Kamu adalah asisten pencatatan keuangan untuk UMKM Indonesia bernama Nusamind.
Tugasmu: ubah kalimat natural berbahasa Indonesia (termasuk bahasa informal/daerah)
menjadi data transaksi keuangan terstruktur.

ATURAN WAJIB:
1. Output HARUS berupa JSON array murni, tanpa teks tambahan, tanpa markdown code block.
2. Setiap transaksi punya field: type ("pemasukan" atau "pengeluaran"), item_name (string),
   quantity (number atau null), amount (number, dalam Rupiah murni tanpa simbol).
3. Konversi singkatan nominal: "75rb"/"75 ribu" -> 75000, "1,5jt" -> 1500000.
4. Jika satu kalimat berisi beberapa transaksi, pisahkan menjadi beberapa elemen array.
5. Jika nominal tidak disebutkan jelas/ambigu, isi amount dengan null (JANGAN MENGARANG angka).
6. Jangan tambahkan field lain selain yang disebutkan di atas.
7. Jangan beri komentar, penjelasan, atau teks pembuka/penutup apa pun.

Format output:
[{"type":"pemasukan","item_name":"...","quantity":0,"amount":0}]';
    }

    public function extractTransactions(string $inputText, int $userId): array
    {
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
                        ['role' => 'user', 'content' => $inputText],
                    ],
                    'temperature' => 0.2,
                ]);

            $raw = $response->json('choices.0.message.content');

            if (!$raw) {
                $this->logUsage($userId, 'finance', 'failed', null);
                throw new AiParsingException();
            }

            $clean = trim($raw);
            $clean = str_replace(['```json', '```', '`'], '', $clean);
            $decoded = json_decode($clean, true);

            if (!is_array($decoded)) {
                $this->logUsage($userId, 'finance', 'failed', null);
                throw new AiParsingException();
            }

            $tokensUsed = $response->json('usage.total_tokens');
            $this->logUsage($userId, 'finance', 'success', $tokensUsed);

            return $decoded;
        } catch (\Exception $e) {
            if ($e instanceof AiParsingException) {
                throw $e;
            }

            Log::error('AI Finance timeout/error: ' . $e->getMessage());
            $this->logUsage($userId, 'finance', 'timeout', null);

            throw new \Exception('Maaf, Nusamind sedang sibuk. Coba lagi ya!');
        }
    }

    private function logUsage(int $userId, string $feature, string $status, ?int $tokens): void
    {
        AiUsageLog::create([
            'user_id' => $userId,
            'feature' => $feature,
            'tokens_used' => $tokens,
            'status' => $status,
        ]);
    }
}
