<?php

namespace App\Http\Controllers;

use App\Models\Issdar;
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

        $latestIssdarat = Issdar::with('categories')
            ->latest()
            ->take(8)
            ->get();

        $popularIssdarat = Issdar::with('categories')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(8)
            ->get();

        $categories = Category::withCount('issdarat')->get();

        $stats = [
            'totalIssdarat' => Issdar::count(),
            'totalViews' => (int) Issdar::sum('views'),
            'totalDownloads' => (int) Issdar::sum('downloads'),
            'totalCategories' => $categories->count(),
        ];

        return Inertia::render('Home', [
            'banners' => $banners,
            'latestIssdarat' => $latestIssdarat,
            'popularIssdarat' => $popularIssdarat,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }
}