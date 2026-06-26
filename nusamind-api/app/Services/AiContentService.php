<?php

namespace App\Services;

use App\Models\AiUsageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiContentService
{
    private function systemPrompt(string $style): string
    {
        return "Kamu adalah copywriter media sosial profesional untuk UMKM Indonesia bernama Nusamind.
Kamu akan menerima sebuah foto produk. Analisis foto tersebut lalu hasilkan konten promosi.

GAYA BAHASA: {$style} (pilihan: formal | gaul | hard_selling)
- formal: sopan, profesional, cocok untuk produk premium/B2B
- gaul: santai, trendi, banyak emoji, gaya Gen-Z
- hard_selling: persuasif, menonjolkan urgensi & manfaat, ada call-to-action kuat

ATURAN WAJIB:
1. Output HARUS JSON murni tanpa markdown, dengan struktur:
   {\"caption_result\":\"...\", \"hashtags_result\":[\"...\",\"...\"], \"whatsapp_template_result\":\"...\"}
2. caption_result: maksimal 3 kalimat, sesuai gaya bahasa yang diminta.
3. hashtags_result: maksimal 10 hashtag relevan dengan produk dan target pasar Indonesia.
4. whatsapp_template_result: 1 paragraf singkat siap kirim ke pelanggan via WhatsApp broadcast.
5. Jangan menyebutkan merk kompetitor atau klaim kesehatan/medis yang tidak bisa dibuktikan dari foto.
6. Jangan beri penjelasan tambahan di luar JSON.";
    }

    public function generateContent(string $imagePath, string $style, int $userId): array
    {
        $endpoint = config('services.ai.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
        $apiKey = config('services.ai.api_key');
        $model = config('services.ai.vision_model', 'llama-3.2-11b-vision-preview');

        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);

            $response = Http::timeout(30)
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $this->systemPrompt($style) . "\n\nAnalisis foto ini dan buat konten promosi dengan gaya bahasa: {$style}.",
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:{$mimeType};base64,{$imageData}",
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                ]);

            $raw = $response->json('choices.0.message.content');

            if (!$raw) {
                $this->logUsage($userId, 'content', 'failed', null);
                throw new \Exception('Gagal menghasilkan konten');
            }

            $clean = trim($raw);
            $clean = str_replace(['```json', '```', '`'], '', $clean);
            $decoded = json_decode($clean, true);

            if (!isset($decoded['caption_result'])) {
                $this->logUsage($userId, 'content', 'failed', null);
                throw new \Exception('Format hasil AI tidak valid');
            }

            $tokensUsed = $response->json('usage.total_tokens');
            $this->logUsage($userId, 'content', 'success', $tokensUsed);

            return $decoded;
        } catch (\Exception $e) {
            Log::error('AI Content error: ' . $e->getMessage());
            $this->logUsage($userId, 'content', 'timeout', null);
            throw new \Exception('Maaf, Nusamind belum bisa memproses fotomu. Coba lagi ya!');
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
