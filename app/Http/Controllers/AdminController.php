<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Review;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalArticles = Article::count();
        $totalViews = Article::sum('views');
        $totalDownloads = Article::sum('downloads');
        $totalReviews = Review::count();
        $averageRating = round(Review::avg('rating') ?? 0, 1);
        $latestArticles = Article::latest()->take(5)->get();
        $latestReviews = Review::with('article')->latest()->take(5)->get();
        $categoriesCount = Category::count();

        return Inertia::render('Admin/Dashboard', [
            'totalArticles' => $totalArticles,
            'totalViews' => $totalViews,
            'totalDownloads' => $totalDownloads,
            'totalReviews' => $totalReviews,
            'averageRating' => $averageRating,
            'categoriesCount' => $categoriesCount,
            'latestArticles' => $latestArticles,
            'latestReviews' => $latestReviews,
        ]);
    }
}