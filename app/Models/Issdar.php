<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issdar extends Model
{
    protected $table = 'issdarat';

    protected $fillable = [
        'title', 'description', 'thumbnail_path', 'file_path',
        'views', 'downloads', 'link', 'release_date',
    ];

    protected $casts = [
        'release_date' => 'date',
        'views' => 'integer',
        'downloads' => 'integer',
    ];

    protected $appends = ['average_rating', 'thumbnail_url', 'file_url'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'issdar_category');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? '/storage/' . $this->thumbnail_path : null;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? '/storage/' . $this->file_path : null;
    }
}