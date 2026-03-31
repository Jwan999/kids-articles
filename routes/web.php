<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\IssdarController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/issdar/{id}', [IssdarController::class, 'show'])->name('issdar.show');
Route::get('/issdar/{id}/download', [IssdarController::class, 'download'])->name('issdar.download');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

// Auth
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard shortcut
Route::get('/dashboard', function () {
    return redirect('/admin');
})->middleware('auth')->name('dashboard');

// Admin (auth middleware)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/issdarat', [IssdarController::class, 'index'])->name('issdarat.index');
    Route::get('/issdarat/create', [IssdarController::class, 'create'])->name('issdarat.create');
    Route::post('/issdarat', [IssdarController::class, 'store'])->name('issdarat.store');
    Route::get('/issdarat/{id}/edit', [IssdarController::class, 'edit'])->name('issdarat.edit');
    Route::put('/issdarat/{id}', [IssdarController::class, 'update'])->name('issdarat.update');
    Route::delete('/issdarat/{id}', [IssdarController::class, 'destroy'])->name('issdarat.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners/group', [BannerController::class, 'updateGroup'])->name('banners.group');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::put('/banners/{id}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');
    Route::patch('/banners/{id}/toggle', [BannerController::class, 'toggleActive'])->name('banners.toggle');

    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});