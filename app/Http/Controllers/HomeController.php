<?php

namespace App\Http\Controllers;

use App\Models\Issdar;
use App\Models\BannerGroup;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $bannerGroup = BannerGroup::where('is_active', true)->first();
        $banners = $bannerGroup
            ? $bannerGroup->banners()->where('is_active', true)->values()
            : collect();

        $latestQuery = Issdar::with('categories');
        $popularQuery = Issdar::with('categories');

        if ($request->filled('category')) {
            $categoryId = $request->category;
            $latestQuery->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId));
            $popularQuery->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId));
        }

        $latestIssdarat = $latestQuery->orderByDesc('release_date')->take(8)->get();

        $popularIssdarat = $popularQuery
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

        $topReviews = Review::with('issdar')
            ->where('rating', 5)
            ->latest()
            ->take(6)
            ->get();

        return Inertia::render('Home', [
            'banners' => $banners,
            'latestIssdarat' => $latestIssdarat,
            'popularIssdarat' => $popularIssdarat,
            'categories' => $categories,
            'stats' => $stats,
            'topReviews' => $topReviews,
        ]);
    }
}