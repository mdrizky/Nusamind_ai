<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AiUsageLogController;
use App\Http\Controllers\Admin\ContentReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\User\UserWebController;
use App\Http\Controllers\User\ReplyWebController;
use App\Http\Controllers\User\StockWebController;
use App\Http\Controllers\User\CampaignWebController;
use App\Http\Controllers\User\LoyalWebController;
use App\Http\Controllers\User\PriceWebController;
use App\Http\Controllers\User\CatalogWebController;
use App\Http\Controllers\User\GlobalWebController;
use App\Http\Controllers\User\ScoreWebController;
use App\Http\Controllers\User\CoachWebController;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'loginWeb']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'registerWeb']);
});

Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout')->middleware('auth');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');

    Route::get('/ai-usage', [AiUsageLogController::class, 'index'])->name('ai-usage.index');

    Route::get('/content-reports', [ContentReportController::class, 'index'])->name('content-reports.index');
    Route::post('/content-reports/{id}/resolve', [ContentReportController::class, 'resolve'])->name('content-reports.resolve');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::post('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/{id}/delete', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/broadcast', [NotificationController::class, 'broadcast'])->name('notifications.broadcast');
});

Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [UserWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/features', [UserWebController::class, 'features'])->name('features');
    Route::get('/business', [UserWebController::class, 'business'])->name('business');
    Route::post('/business', [UserWebController::class, 'businessUpdate'])->name('business.update');
    Route::put('/business', [UserWebController::class, 'businessUpdate'])->name('business.update.put');
    Route::get('/products', [UserWebController::class, 'products'])->name('products.index');
    Route::get('/products/create', [UserWebController::class, 'productCreate'])->name('products.create');
    Route::post('/products', [UserWebController::class, 'productStore'])->name('products.store');
    Route::get('/products/{id}/edit', [UserWebController::class, 'productEdit'])->name('products.edit');
    Route::put('/products/{id}', [UserWebController::class, 'productUpdate'])->name('products.update');
    Route::delete('/products/{id}', [UserWebController::class, 'productDestroy'])->name('products.destroy');
    Route::get('/transactions', [UserWebController::class, 'transactions'])->name('transactions');
    Route::get('/content', [UserWebController::class, 'contentHistory'])->name('content.index');
    Route::get('/notifications', [UserWebController::class, 'notifications'])->name('notifications.index');
    Route::put('/notifications/{id}/read', [UserWebController::class, 'markNotificationRead'])->name('notifications.read');
    Route::get('/profile', [UserWebController::class, 'profile'])->name('profile');
    Route::post('/profile/password', [UserWebController::class, 'updatePassword'])->name('profile.update-password');

    // NusaReply
    Route::get('/reply', [ReplyWebController::class, 'index'])->name('reply.index');
    Route::post('/reply/generate', [ReplyWebController::class, 'generateReply'])->name('reply.generate');
    Route::get('/reply/faq', [ReplyWebController::class, 'faqIndex'])->name('reply.faq');
    Route::post('/reply/faq', [ReplyWebController::class, 'faqStore'])->name('reply.faq.store');
    Route::delete('/reply/faq/{id}', [ReplyWebController::class, 'faqDestroy'])->name('reply.faq.destroy');
    Route::post('/reply/{id}/save', [ReplyWebController::class, 'saveReply'])->name('reply.save');
    Route::get('/reply/saved', [ReplyWebController::class, 'history'])->name('reply.saved');

    // NusaStock
    Route::get('/stock', [StockWebController::class, 'index'])->name('stock.index');
    Route::post('/stock/ai-recommend', [StockWebController::class, 'aiRecommend'])->name('stock.ai-recommend');
    Route::get('/stock/movements', [StockWebController::class, 'movements'])->name('stock.movements');
    Route::post('/stock/adjust', [StockWebController::class, 'adjustStock'])->name('stock.adjust');

    // NusaCampaign
    Route::get('/campaign', [CampaignWebController::class, 'index'])->name('campaign.index');
    Route::post('/campaign/generate', [CampaignWebController::class, 'generate'])->name('campaign.generate');
    Route::post('/campaign/{id}/activate', [CampaignWebController::class, 'activate'])->name('campaign.activate');
    Route::delete('/campaign/{id}', [CampaignWebController::class, 'delete'])->name('campaign.delete');

    // NusaLoyal
    Route::get('/loyal', [LoyalWebController::class, 'index'])->name('loyal.index');
    Route::post('/loyal/customer', [LoyalWebController::class, 'store'])->name('loyal.customer.store');
    Route::put('/loyal/customer/{id}', [LoyalWebController::class, 'update'])->name('loyal.customer.update');
    Route::delete('/loyal/customer/{id}', [LoyalWebController::class, 'destroy'])->name('loyal.customer.destroy');
    Route::post('/loyal/follow-up', [LoyalWebController::class, 'generateFollowUp'])->name('loyal.follow-up');

    // NusaPrice
    Route::get('/price', [PriceWebController::class, 'index'])->name('price.index');
    Route::post('/price/recommend', [PriceWebController::class, 'recommend'])->name('price.recommend');

    // NusaCatalog
    Route::get('/catalog', [CatalogWebController::class, 'index'])->name('catalog.index');
    Route::post('/catalog/enhance', [CatalogWebController::class, 'enhance'])->name('catalog.enhance');
    Route::post('/catalog/apply/{id}', [CatalogWebController::class, 'apply'])->name('catalog.apply');

    // NusaGlobal
    Route::get('/global', [GlobalWebController::class, 'index'])->name('global.index');
    Route::post('/global/translate', [GlobalWebController::class, 'translate'])->name('global.translate');

    // NusaScore
    Route::get('/score', [ScoreWebController::class, 'index'])->name('score.index');
    Route::get('/score/history', [ScoreWebController::class, 'history'])->name('score.history');

    // NusaCoach
    Route::get('/coach', [CoachWebController::class, 'index'])->name('coach.index');
    Route::post('/coach/chat', [CoachWebController::class, 'chat'])->name('coach.chat');
    Route::post('/coach/clear', [CoachWebController::class, 'clear'])->name('coach.clear');
});
