<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BannerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();
        $bannerGroups = BannerGroup::all();

        return Inertia::render('Admin/Banners/Index', [
            'banners' => $banners,
            'bannerGroups' => $bannerGroups,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'image' => 'required|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'link' => $validated['link'] ?? null,
            'image_path' => $imagePath,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'تم إنشاء البانر بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'link' => $validated['link'] ?? null,
            'is_active' => $validated['is_active'] ?? $banner->is_active,
        ];

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image_path);
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->back()->with('success', 'تم تحديث البانر بنجاح.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'تم حذف البانر بنجاح.');
    }

    public function toggleActive($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['is_active' => !$banner->is_active]);

        return redirect()->back()->with('success', 'تم تحديث حالة البانر.');
    }

    public function updateGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'banner_ids' => 'required|array|max:3',
            'is_active' => 'boolean',
        ]);

        $bannerIds = collect($validated['banner_ids'])->map(fn ($id) => (int) $id)->values()->toArray();

        BannerGroup::updateOrCreate(
            ['name' => $validated['name']],
            [
                'banner_ids' => $bannerIds,
                'is_active' => $validated['is_active'] ?? true,
            ]
        );

        return redirect()->back()->with('success', 'تم تحديث مجموعة البانرات بنجاح.');
    }
}