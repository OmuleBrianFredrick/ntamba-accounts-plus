<?php

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
 * M1 Foundation API. One Laravel back end serves the agent PWA and the
 * accountant console. Roles are checked on every request.
 */

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Read access: any signed-in role (company scope + field masking apply).
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/routes', [RouteController::class, 'index']);
    Route::get('/settings', [SettingController::class, 'index']);

    // Admin: users & roles, routes, settings, devices.
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);

        Route::post('/routes', [RouteController::class, 'store']);
        Route::put('/routes/{route}', [RouteController::class, 'update']);

        Route::put('/settings', [SettingController::class, 'update']);

        Route::get('/devices', [DeviceController::class, 'index']);
        Route::post('/devices/{device}/sign-out', [DeviceController::class, 'signOut']);
    });

    // Products & prices: admin manages; accountant edits prices later.
    Route::middleware('role:admin,accountant')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
    });

    // Audit log read: accountant + admin only (agents never see it).
    Route::middleware('role:accountant,admin')->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index']);
    });
});
