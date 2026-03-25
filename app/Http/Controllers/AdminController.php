<?php

namespace App\Http\Controllers;

use App\Models\Issdar;
use App\Models\Category;
use App\Models\Review;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalIssdarat = Issdar::count();
        $totalViews = Issdar::sum('views');
        $totalDownloads = Issdar::sum('downloads');
        $totalReviews = Review::count();
        $averageRating = round(Review::avg('rating') ?? 0, 1);
        $latestIssdarat = Issdar::latest()->take(5)->get();
        $latestReviews = Review::with('issdar')->latest()->take(5)->get();
        $categoriesCount = Category::count();

        return Inertia::render('Admin/Dashboard', [
            'totalIssdarat' => $totalIssdarat,
            'totalViews' => $totalViews,
            'totalDownloads' => $totalDownloads,
            'totalReviews' => $totalReviews,
            'averageRating' => $averageRating,
            'categoriesCount' => $categoriesCount,
            'latestIssdarat' => $latestIssdarat,
            'latestReviews' => $latestReviews,
        ]);
    }
}