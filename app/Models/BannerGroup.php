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
        return Banner::whereIn('id', $ids)->get();
    }
}
