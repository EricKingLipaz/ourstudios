<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\ConfigController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/payment-methods', [ConfigController::class, 'getPaymentMethods']);
Route::get('/bank-details', [ConfigController::class, 'getBankDetails']);

// Booking routes (public can create, admin can manage)
Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings/{id}', [BookingController::class, 'show']);

// Payment routes
Route::post('/payments/upload-proof', [PaymentController::class, 'uploadProof']);

// Admin routes (add authentication middleware in production)
Route::prefix('admin')->group(function () {
    // Bookings management
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
    Route::get('/bookings/statistics', [BookingController::class, 'statistics']);
    
    // Payment management
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::put('/payments/{id}/verify', [PaymentController::class, 'verify']);
    Route::put('/payments/{id}/reject', [PaymentController::class, 'reject']);
    Route::get('/payments/export', [PaymentController::class, 'export']);
    
    // Service management
    Route::get('/services', [ServiceController::class, 'all']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    
    // Bank details management
    Route::put('/bank-details/{id}', [ConfigController::class, 'updateBankDetails']);
});
