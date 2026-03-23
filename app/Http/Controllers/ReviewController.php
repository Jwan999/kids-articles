<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);

        $ipAddress = $request->ip();

        $existingReview = Review::where('article_id', $validated['article_id'])
            ->where('ip_address', $ipAddress)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'لقد قمت بتقييم هذا المقال مسبقاً.');
        }

        Review::create([
            'article_id' => $validated['article_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'rating' => $validated['rating'],
            'review' => $validated['review'],
            'ip_address' => $ipAddress,
        ]);

        return redirect()->back()->with('success', 'تم إرسال التقييم بنجاح.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'تم حذف التقييم بنجاح.');
    }
}