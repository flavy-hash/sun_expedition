<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'name',
        'email',
        'country',
        'rating',
        'title',
        'quote',
        'images',
        'is_approved',
    ];

    protected $casts = [
        'images' => 'array',
        'is_approved' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleted(fn (Testimonial $review) => Storage::disk('public')->delete($review->images ?? []));
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function scopeApproved(Builder $query): void
    {
        $query->where('is_approved', true);
    }
}
