<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/article/{id}', [ArticleController::class, 'show'])->name('article.show');
Route::get('/article/{id}/download', [ArticleController::class, 'download'])->name('article.download');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

// Auth
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin (auth middleware)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::put('/banners/{id}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');
    Route::patch('/banners/{id}/toggle', [BannerController::class, 'toggleActive'])->name('banners.toggle');
    Route::post('/banners/group', [BannerController::class, 'updateGroup'])->name('banners.group');

    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});