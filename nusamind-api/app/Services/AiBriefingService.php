<?php

namespace App\Services;

use App\Models\AiUsageLog;
use App\Models\BusinessInsight;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiBriefingService
{
    private function systemPrompt(): string
    {
        return 'Kamu adalah partner bisnis virtual untuk UMKM Indonesia bernama Nusamind.
Kamu akan menerima data ringkas penjualan mingguan dalam format JSON.
Tugasmu: ubah data tersebut menjadi narasi singkat, hangat, dan mudah dipahami
oleh pemilik usaha kecil yang TIDAK paham istilah bisnis/statistik.

ATURAN WAJIB:
1. Gunakan bahasa Indonesia santai, seperti partner bisnis yang peduli, bukan robot formal.
2. Sebutkan produk paling laku jika datanya tersedia.
3. Sebutkan produk dengan stok menipis HANYA jika data stok diberikan (jangan mengarang).
4. Beri satu saran praktis singkat di akhir (misal soal restock atau promosi).
5. Maksimal 4 kalimat.
6. Output berupa teks biasa (bukan JSON), sapa dengan "Kak" atau "Bos".';
    }

    public function generateBriefing(User $user): ?BusinessInsight
    {
        $periodStart = now()->subDays(7)->startOfDay();
        $periodEnd = now()->endOfDay();

        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_date', [$periodStart, $periodEnd])
            ->get();

        if ($transactions->isEmpty()) {
            return null;
        }

        $totalIncome = (int) $transactions->where('type', 'pemasukan')->sum('amount');
        $totalExpense = (int) $transactions->where('type', 'pengeluaran')->sum('amount');

        $topProductData = $transactions->where('type', 'pemasukan')
            ->groupBy('item_name')
            ->map(fn($items) => $items->sum('quantity'))
            ->sortDesc()
            ->first();

        $topProductName = $transactions->where('type', 'pemasukan')
            ->groupBy('item_name')
            ->map(fn($items) => ['qty' => $items->sum('quantity'), 'name' => $items->first()->item_name])
            ->sortByDesc('qty')
            ->first();

        $business = $user->business;
        $lowStockProduct = null;
        if ($business) {
            $lowStockProduct = Product::where('business_id', $business->id)
                ->whereNotNull('stock')
                ->where('stock', '>', 0)
                ->orderBy('stock', 'asc')
                ->first();
        }

        $aiData = [
            'top_product' => $topProductName['name'] ?? null,
            'top_product_qty' => $topProductName['qty'] ?? 0,
            'low_stock_product' => $lowStockProduct?->name,
            'low_stock_qty' => $lowStockProduct?->stock ?? 0,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
        ];

        $apiKey = config('services.ai.api_key');
        $endpoint = config('services.ai.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
        $model = config('services.ai.model', 'llama-3.3-70b-versatile');

        try {
            $response = Http::timeout(15)
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => json_encode($aiData)],
                    ],
                    'temperature' => 0.7,
                ]);

            $narrative = $response->json('choices.0.message.content');

            if (!$narrative) {
                $narrative = $this->fallbackNarrative($aiData);
            }

            $this->logUsage($user->id, 'briefing', 'success', $response->json('usage.total_tokens'));

            return BusinessInsight::create([
                'user_id' => $user->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'narrative_text' => $narrative,
                'top_product' => $aiData['top_product'],
                'low_stock_product' => $aiData['low_stock_product'],
            ]);
        } catch (\Exception $e) {
            Log::error('AI Briefing error: ' . $e->getMessage());
            $this->logUsage($user->id, 'briefing', 'timeout', null);

            $narrative = $this->fallbackNarrative($aiData);

            return BusinessInsight::create([
                'user_id' => $user->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'narrative_text' => $narrative,
                'top_product' => $aiData['top_product'],
                'low_stock_product' => $aiData['low_stock_product'],
            ]);
        }
    }

    private function fallbackNarrative(array $data): string
    {
        $text = "Halo Kak! ";
        if ($data['top_product']) {
            $text .= "Produk {$data['top_product']} jadi primadona minggu ini, laku {$data['top_product_qty']} unit! ";
        }
        if ($data['low_stock_product'] && $data['low_stock_qty'] > 0 && $data['low_stock_qty'] <= 10) {
            $text .= "Eh, stok {$data['low_stock_product']} tinggal {$data['low_stock_qty']} nih, mending restok sekarang. ";
        }
        $text .= "Total pemasukan Rp" . number_format($data['total_income'], 0, ',', '.') . ", lumayan banget!";
        return $text;
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
