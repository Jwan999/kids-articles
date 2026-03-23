<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function show($id)
    {
        $article = Article::with(['categories', 'reviews'])->findOrFail($id);
        $article->increment('views');

        $categoryIds = $article->categories->pluck('id');
        $relatedArticles = Article::with('categories')
            ->where('id', '!=', $article->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->take(4)
            ->get();

        return Inertia::render('Article', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }

    public function index(Request $request)
    {
        $query = Article::with('categories');

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

        $articles = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'sort']),
        ]);
    }

    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Admin/Articles/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'required|image|max:2048',
            'file' => 'required|file|max:10240',
            'link' => 'nullable|url|max:255',
            'release_date' => 'nullable|date',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        $filePath = $request->file('file')->store('articles', 'public');

        $article = Article::create([
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
            $article->categories()->attach($validated['categories']);
        }

        return redirect()->route('admin.articles.index')->with('success', 'تم إنشاء المقال بنجاح.');
    }

    public function edit($id)
    {
        $article = Article::with('categories')->findOrFail($id);
        $categories = Category::all();

        return Inertia::render('Admin/Articles/Edit', [
            'article' => $article,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'file' => 'nullable|file|max:10240',
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
            Storage::disk('public')->delete($article->thumbnail_path);
            $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($article->file_path);
            $data['file_path'] = $request->file('file')->store('articles', 'public');
        }

        $article->update($data);

        $article->categories()->sync($validated['categories'] ?? []);

        return redirect()->back()->with('success', 'تم تحديث المقال بنجاح.');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if ($article->thumbnail_path) {
            Storage::disk('public')->delete($article->thumbnail_path);
        }

        if ($article->file_path) {
            Storage::disk('public')->delete($article->file_path);
        }

        $article->delete();

        return redirect()->back()->with('success', 'تم حذف المقال بنجاح.');
    }

    public function download($id)
    {
        $article = Article::findOrFail($id);
        $article->increment('downloads');

        return Storage::disk('public')->download($article->file_path, $article->title);
    }
}