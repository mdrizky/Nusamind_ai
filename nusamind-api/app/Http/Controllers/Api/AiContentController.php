<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\ContentGeneration;
use App\Models\ContentReport;
use App\Services\AiContentService;
use App\Services\AiQuotaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AiContentController extends Controller
{
    public function __construct(
        private AiContentService $aiContentService,
        private AiQuotaService $aiQuotaService
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png|max:5120',
            'style' => 'required|in:formal,gaul,hard_selling',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $userId = $request->user()->id;

        if (!$this->aiQuotaService->check($userId)) {
            return response()->json([
                'message' => 'Kamu sudah mencapai batas pemakaian AI hari ini (30x). Besok lagi ya!',
            ], 429);
        }

        $image = $request->file('image');

        $dimensions = getimagesize($image);
        if ($dimensions && ($dimensions[0] < 300 || $dimensions[1] < 300)) {
            return response()->json([
                'message' => 'Foto minimal 300x300 pixel ya',
            ], 422);
        }

        $imagePath = $image->store('content-images', 'public');

        try {
            $result = $this->aiContentService->generateContent(
                Storage::disk('public')->path($imagePath),
                $validated['style'],
                $userId
            );

            $content = ContentGeneration::create([
                'user_id' => $userId,
                'product_id' => $validated['product_id'] ?? null,
                'image_path' => $imagePath,
                'style' => $validated['style'],
                'caption_result' => $result['caption_result'] ?? null,
                'hashtags_result' => $result['hashtags_result'] ?? [],
                'whatsapp_template_result' => $result['whatsapp_template_result'] ?? null,
            ]);

            $this->aiQuotaService->checkAndIncrement($userId, 'content');

            return response()->json([
                'caption_result' => $content->caption_result,
                'hashtags_result' => $content->hashtags_result,
                'whatsapp_template_result' => $content->whatsapp_template_result,
                'content_id' => $content->id,
            ]);
        } catch (\Exception $e) {
            AiUsageLog::create([
                'user_id' => $userId,
                'feature' => 'content',
                'status' => 'timeout',
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    public function regenerate(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;

        $content = ContentGeneration::where('user_id', $userId)->findOrFail($id);

        $regenerateCount = ContentGeneration::where('user_id', $userId)
            ->where('id', $id)
            ->whereDate('created_at', today())
            ->count();

        if ($regenerateCount > 5) {
            return response()->json([
                'message' => 'Kamu sudah regenerate konten ini 5x hari ini, coba lagi besok ya!',
            ], 429);
        }

        $imageFullPath = Storage::disk('public')->path($content->image_path);

        try {
            $result = $this->aiContentService->generateContent(
                $imageFullPath,
                $content->style,
                $userId
            );

            $content->update([
                'caption_result' => $result['caption_result'] ?? $content->caption_result,
                'hashtags_result' => $result['hashtags_result'] ?? $content->hashtags_result,
                'whatsapp_template_result' => $result['whatsapp_template_result'] ?? $content->whatsapp_template_result,
            ]);

            $this->aiQuotaService->checkAndIncrement($userId, 'content');

            return response()->json([
                'caption_result' => $content->caption_result,
                'hashtags_result' => $content->hashtags_result,
                'whatsapp_template_result' => $content->whatsapp_template_result,
                'content_id' => $content->id,
            ]);
        } catch (\Exception $e) {
            AiUsageLog::create([
                'user_id' => $userId,
                'feature' => 'content',
                'status' => 'timeout',
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    public function history(Request $request): JsonResponse
    {
        $contents = ContentGeneration::where('user_id', $request->user()->id)
            ->with('product:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'content_generations' => $contents,
        ]);
    }

    public function report(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content_generation_id' => 'required|exists:content_generations,id',
            'reason' => 'required|string|min:10',
        ]);

        $report = ContentReport::create([
            'content_generation_id' => $validated['content_generation_id'],
            'reported_by' => $request->user()->id,
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'Laporan terkirim, terima kasih sudah membantu menjaga kualitas konten!',
            'report' => $report,
        ], 201);
    }
}
