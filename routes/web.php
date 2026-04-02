<?php

use App\Http\Controllers\AiAnalysisController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// ── Guest routes ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ── Authenticated routes ───────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', fn () => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::resource('transactions', TransactionController::class)
        ->except(['show']);

    // Categories
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'create', 'store', 'destroy']);

    // AI Analysis
    Route::get('/ai-analysis', [AiAnalysisController::class, 'index'])->name('ai.analysis');
    Route::post('/ai-analysis', [AiAnalysisController::class, 'analyze'])->name('ai.analyze');

    // Theme toggle
    Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
