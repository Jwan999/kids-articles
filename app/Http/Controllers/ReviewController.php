<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Issdar;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('issdar');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('review', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();
        $issdarat = Issdar::select('id', 'title')->orderBy('title')->get();

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews,
            'issdarat' => $issdarat,
            'filters' => $request->only(['search', 'rating']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'issdar_id' => 'required|exists:issdarat,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);

        // Skip IP check if admin is authenticated
        if (!auth()->check()) {
            $ipAddress = $request->ip();

            $existingReview = Review::where('issdar_id', $validated['issdar_id'])
                ->where('ip_address', $ipAddress)
                ->first();

            if ($existingReview) {
                return redirect()->back()->with('error', 'لقد قمت بتقييم هذا الإصدار مسبقاً.');
            }

            $validated['ip_address'] = $ipAddress;
        }

        Review::create($validated);

        return redirect()->back()->with('success', 'تم إضافة التقييم بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);

        $review->update($validated);

        return redirect()->back()->with('success', 'تم تحديث التقييم بنجاح.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'تم حذف التقييم بنجاح.');
    }
}
