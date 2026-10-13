<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'circuit',
        'title',
        'slug',
        'summary',
        'description',
        'duration_days',
        'price_from',
        'rating',
        'reviews_count',
        'difficulty',
        'group_size_max',
        'best_time',
        'highlight_stat',
        'location',
        'hero_image',
        'gallery',
        'highlights',
        'included',
        'excluded',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'gallery' => 'array',
        'highlights' => 'array',
        'included' => 'array',
        'excluded' => 'array',
        'is_featured' => 'boolean',
    ];

    public function itineraryItems(): HasMany
    {
        return $this->hasMany(ItineraryItem::class)->orderBy('day_number');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
