<?php

namespace App\Http\Controllers;

use App\Models\Issdar;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class IssdarController extends Controller
{
    public function show($id)
    {
        $issdar = Issdar::with(['categories', 'reviews'])->findOrFail($id);
        $issdar->increment('views');

        $categoryIds = $issdar->categories->pluck('id');
        $relatedIssdarat = Issdar::with('categories')
            ->where('id', '!=', $issdar->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->take(4)
            ->get();

        return Inertia::render('Issdar', [
            'issdar' => $issdar,
            'relatedIssdarat' => $relatedIssdarat,
        ]);
    }

    public function index(Request $request)
    {
        $query = Issdar::with('categories');

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
                break;
            case 'views':
                $query->orderByDesc('views');
                break;
            case 'downloads':
                $query->orderByDesc('downloads');
                break;
            case 'release_date':
                $query->orderByDesc('release_date');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $issdarat = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return Inertia::render('Admin/Issdarat/Index', [
            'issdarat' => $issdarat,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'sort']),
        ]);
    }

    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Admin/Issdarat/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'required|image|max:2048',
            'file' => 'required|file|max:102400',
            'link' => 'nullable|url|max:255',
            'release_date' => 'nullable|date',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        $filePath = $request->file('file')->store('issdarat', 'public');

        $issdar = Issdar::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'thumbnail_path' => $thumbnailPath,
            'file_path' => $filePath,
            'link' => $validated['link'] ?? null,
            'release_date' => $validated['release_date'] ?? null,
            'views' => 0,
            'downloads' => 0,
        ]);

        if (!empty($validated['categories'])) {
            $issdar->categories()->attach($validated['categories']);
        }

        return redirect()->route('admin.issdarat.index')->with('success', 'تم إنشاء الإصدار بنجاح.');
    }

    public function edit($id)
    {
        $issdar = Issdar::with('categories')->findOrFail($id);
        $categories = Category::all();

        return Inertia::render('Admin/Issdarat/Edit', [
            'issdar' => $issdar,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $issdar = Issdar::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'file' => 'nullable|file|max:102400',
            'link' => 'nullable|url|max:255',
            'release_date' => 'nullable|date',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'link' => $validated['link'] ?? null,
            'release_date' => $validated['release_date'] ?? null,
        ];

        if ($request->hasFile('thumbnail')) {
            Storage::disk('public')->delete($issdar->thumbnail_path);
            $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($issdar->file_path);
            $data['file_path'] = $request->file('file')->store('issdarat', 'public');
        }

        $issdar->update($data);

        $issdar->categories()->sync($validated['categories'] ?? []);

        return redirect()->back()->with('success', 'تم تحديث الإصدار بنجاح.');
    }

    public function destroy($id)
    {
        $issdar = Issdar::findOrFail($id);

        if ($issdar->thumbnail_path) {
            Storage::disk('public')->delete($issdar->thumbnail_path);
        }

        if ($issdar->file_path) {
            Storage::disk('public')->delete($issdar->file_path);
        }

        $issdar->delete();

        return redirect()->back()->with('success', 'تم حذف الإصدار بنجاح.');
    }

    public function download($id)
    {
        $issdar = Issdar::findOrFail($id);
        $issdar->increment('downloads');

        return Storage::disk('public')->download($issdar->file_path, $issdar->title);
    }
}