<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AiResponseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::put('/user', [AuthController::class, 'update'])->middleware('auth:sanctum');
Route::put('/user/notification-settings', [AuthController::class, 'updateNotificationSettings'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Business
    Route::get('/businesses', [BusinessController::class, 'index']);
    Route::post('/businesses', [BusinessController::class, 'store']);
    Route::get('/businesses/{business}', [BusinessController::class, 'show']);
    Route::put('/businesses/{business}', [BusinessController::class, 'update']);
    Route::delete('/businesses/{business}', [BusinessController::class, 'destroy']);
    Route::post('/businesses/{business}/responses', [ResponseController::class, 'generate']);
    Route::post('/reviews/{review}/generate', [ResponseController::class, 'generate']);

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'import']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    // Response
    Route::post('/responses/generate', [ResponseController::class, 'generate']);
    Route::get('/responses/templates', [ResponseController::class, 'templates']);
    Route::get('/responses/provider', [ResponseController::class, 'getProvider']);

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index']);

    // Usage
    Route::get('/usage', [UsageController::class, 'index']);

    // Subscription
    Route::get('/subscription', [SubscriptionController::class, 'show']);
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout']);

    // Integrations
    Route::get('/integrations/status', [IntegrationController::class, 'status']);
    Route::post('/integrations/google/search', [IntegrationController::class, 'searchGoogle']);
    Route::post('/integrations/google/sync', [IntegrationController::class, 'syncGoogle']);
    Route::post('/integrations/yelp/search', [IntegrationController::class, 'searchYelp']);
    Route::post('/integrations/yelp/sync', [IntegrationController::class, 'syncYelp']);

    // Team Management
    Route::get('/businesses/{business}/team', [TeamController::class, 'index']);
    Route::post('/businesses/{business}/team/invite', [TeamController::class, 'invite']);
    Route::put('/businesses/{business}/team/{member}/role', [TeamController::class, 'updateRole']);
    Route::delete('/businesses/{business}/team/{member}', [TeamController::class, 'remove']);
    Route::get('/businesses/{business}/permissions', [TeamController::class, 'getPermissions']);

    // Review Requests
    Route::post('/businesses/{business}/send-review-request', [TeamController::class, 'sendReviewRequest']);
});
