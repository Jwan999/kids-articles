<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title', 'subtitle', 'link', 'image_path', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? '/storage/' . $this->image_path : null;
    }
}
