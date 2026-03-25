<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['issdar_id', 'name', 'email', 'rating', 'review', 'ip_address'];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function issdar(): BelongsTo
    {
        return $this->belongsTo(Issdar::class);
    }
}