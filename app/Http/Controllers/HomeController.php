<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BannerGroup;
use App\Models\Category;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $bannerGroup = BannerGroup::where('is_active', true)->first();
        $banners = $bannerGroup
            ? $bannerGroup->banners()->where('is_active', true)->values()
            : collect();

        $latestArticles = Article::with('categories')
            ->latest()
            ->take(8)
            ->get();

        $popularArticles = Article::with('categories')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(8)
            ->get();

        $categories = Category::withCount('articles')->get();

        $stats = [
            'totalArticles' => Article::count(),
            'totalViews' => (int) Article::sum('views'),
            'totalDownloads' => (int) Article::sum('downloads'),
            'totalCategories' => $categories->count(),
        ];

        return Inertia::render('Home', [
            'banners' => $banners,
            'latestArticles' => $latestArticles,
            'popularArticles' => $popularArticles,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }
}