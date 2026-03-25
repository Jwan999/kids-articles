<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('issdarat')->get();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return redirect()->back()->with('success', 'تم إنشاء التصنيف بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'تم تحديث التصنيف بنجاح.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->issdarat()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف تصنيف مرتبط بإصدارات.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'تم حذف التصنيف بنجاح.');
    }
}