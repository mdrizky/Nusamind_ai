<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\AiFinanceController;
use App\Http\Controllers\Api\AiContentController;
use App\Http\Controllers\Api\BusinessInsightController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AdminAiUsageController;
use App\Http\Controllers\Api\AdminContentReportController;
use App\Http\Controllers\Api\AdminNotificationController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\AiReplyApiController;
use App\Http\Controllers\Api\FaqApiController;
use App\Http\Controllers\Api\AiStockApiController;
use App\Http\Controllers\Api\CampaignApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\AiLoyalApiController;
use App\Http\Controllers\Api\AiPriceApiController;
use App\Http\Controllers\Api\AiCatalogApiController;
use App\Http\Controllers\Api\HealthScoreApiController;
use App\Http\Controllers\Api\AiCoachApiController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'check.suspended'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::post('/business', [BusinessController::class, 'store']);
    Route::get('/business/me', [BusinessController::class, 'show']);
    Route::put('/business/me', [BusinessController::class, 'update']);

    Route::apiResource('products', ProductController::class);

    Route::post('/ai/finance/extract', [AiFinanceController::class, 'extract']);
    Route::post('/transactions', [TransactionController::class, 'storeBatch']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

    Route::post('/ai/content/generate', [AiContentController::class, 'generate']);
    Route::post('/ai/content/{id}/regenerate', [AiContentController::class, 'regenerate']);
    Route::get('/content-generations', [AiContentController::class, 'history']);
    Route::post('/content-reports', [AiContentController::class, 'report']);

    Route::get('/business-insights/latest', [BusinessInsightController::class, 'latest']);
    Route::get('/business-insights/history', [BusinessInsightController::class, 'history']);

    Route::post('/ai/export/translate', [ExportController::class, 'translate']);

    // NusaReply
    Route::post('/ai/reply/generate', [AiReplyApiController::class, 'generate']);
    Route::get('/customer-replies', [AiReplyApiController::class, 'index']);
    Route::post('/customer-replies/{id}/save', [AiReplyApiController::class, 'save']);
    Route::apiResource('faqs', FaqApiController::class)->only(['index', 'store', 'destroy']);

    // NusaStock
    Route::post('/ai/stock/analyze', [AiStockApiController::class, 'analyze']);
    Route::get('/stock-movements', [AiStockApiController::class, 'movements']);
    Route::post('/stock-movements', [AiStockApiController::class, 'storeMovement']);

    // NusaCampaign
    Route::get('/campaign-plans', [CampaignApiController::class, 'index']);
    Route::post('/ai/campaign/generate', [CampaignApiController::class, 'generate']);
    Route::delete('/campaign-plans/{id}', [CampaignApiController::class, 'destroy']);

    // NusaLoyal
    Route::apiResource('customers', CustomerApiController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/ai/loyal/follow-up', [AiLoyalApiController::class, 'generateFollowUp']);

    // NusaPrice
    Route::post('/ai/price/recommend', [AiPriceApiController::class, 'recommend']);

    // NusaCatalog
    Route::post('/ai/catalog/enhance', [AiCatalogApiController::class, 'enhance']);

    // NusaScore
    Route::get('/health-scores/latest', [HealthScoreApiController::class, 'latest']);
    Route::get('/health-scores/history', [HealthScoreApiController::class, 'history']);

    // NusaCoach
    Route::post('/ai/coach/chat', [AiCoachApiController::class, 'chat']);

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard/summary', [AdminDashboardController::class, 'summary']);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::put('/users/{id}/suspend', [AdminUserController::class, 'suspend']);
        Route::put('/users/{id}/activate', [AdminUserController::class, 'activate']);
        Route::get('/ai-usage-logs', [AdminAiUsageController::class, 'index']);
        Route::get('/content-reports', [AdminContentReportController::class, 'index']);
        Route::put('/content-reports/{id}/resolve', [AdminContentReportController::class, 'resolve']);
        Route::post('/notifications/broadcast', [AdminNotificationController::class, 'broadcast']);
    });
});
