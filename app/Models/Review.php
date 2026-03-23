<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['article_id', 'name', 'email', 'rating', 'review', 'ip_address'];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
