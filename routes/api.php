<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateUser']);
    Route::put('/user/notification-settings', [AuthController::class, 'updateNotificationSettings']);

    // Businesses
    Route::get('/businesses', [BusinessController::class, 'index']);
    Route::post('/businesses', [BusinessController::class, 'store']);
    Route::get('/businesses/{business}', [BusinessController::class, 'show']);
    Route::put('/businesses/{business}', [BusinessController::class, 'update']);
    Route::delete('/businesses/{business}', [BusinessController::class, 'destroy']);

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews/{review}', [ReviewController::class, 'show']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    Route::post('/reviews/import', [ReviewController::class, 'import']);

    // AI Responses
    Route::post('/reviews/{review}/generate-response', [ResponseController::class, 'generate']);
    Route::post('/responses/{response}/regenerate', [ResponseController::class, 'regenerate']);
    Route::get('/reviews/{review}/responses', [ResponseController::class, 'history']);

    // Subscription
    Route::get('/subscription', [SubscriptionController::class, 'show']);
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout']);

    // Usage
    Route::get('/usage', [UsageController::class, 'index']);

    // Integrations (Google/Yelp)
    Route::get('/integrations/status', [IntegrationController::class, 'status']);
    Route::post('/integrations/google/search', [IntegrationController::class, 'searchGoogle']);
    Route::post('/integrations/google/sync', [IntegrationController::class, 'syncGoogle']);
    Route::post('/integrations/yelp/search', [IntegrationController::class, 'searchYelp']);
    Route::post('/integrations/yelp/sync', [IntegrationController::class, 'syncYelp']);
    Route::post('/integrations/sync-all', [IntegrationController::class, 'syncAll']);

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index']);

    // Team Management
    Route::get('/businesses/{business}/team', [TeamController::class, 'index']);
    Route::post('/businesses/{business}/team/invite', [TeamController::class, 'invite']);
    Route::put('/businesses/{business}/team/{member}/role', [TeamController::class, 'updateRole']);
    Route::delete('/businesses/{business}/team/{member}', [TeamController::class, 'remove']);
    Route::get('/businesses/{business}/permissions', [TeamController::class, 'getPermissions']);

    // Review Requests
    Route::post('/businesses/{business}/send-review-request', [TeamController::class, 'sendReviewRequest']);
});

// Stripe Webhook (no auth, uses signature verification)
Route::post('/webhooks/stripe', [SubscriptionController::class, 'webhook']);
