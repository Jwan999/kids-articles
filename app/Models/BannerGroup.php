<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerGroup extends Model
{
    protected $fillable = ['name', 'banner_ids', 'is_active'];

    protected $casts = [
        'banner_ids' => 'array',
        'is_active' => 'boolean',
    ];

    public function banners()
    {
        $ids = $this->banner_ids ?? [];
        if (empty($ids)) {
            return collect();
        }
        $banners = Banner::whereIn('id', $ids)->get();
        // Preserve the order from banner_ids
        return collect($ids)->map(fn ($id) => $banners->firstWhere('id', $id))->filter();
    }
}
