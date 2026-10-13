<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Accommodation extends Model
{
    public const LEVELS = ['Budget', 'Mid-range', 'Luxury'];

    public const TYPES = ['Lodge', 'Tented camp', 'Boutique hotel', 'Beach resort', 'Farmhouse', 'Mountain hut'];

    public const SUGGESTED_LOCATIONS = ['Arusha', 'Moshi', 'Tarangire', 'Lake Manyara', 'Karatu', 'Ngorongoro', 'Serengeti', 'Ruaha', 'Nyerere', 'Zanzibar'];

    protected $fillable = [
        'name',
        'location',
        'type',
        'level',
        'description',
        'image',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }

    protected function locationKey(): Attribute
    {
        return Attribute::get(fn (): string => Str::slug($this->location));
    }

    public function imageUrl(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        return Str::startsWith($this->image, ['http://', 'https://']) ? $this->image : asset($this->image);
    }

    /** @return array<string, string> slug => location, for every location currently in use. */
    public static function locationOptions(bool $publishedOnly = true): array
    {
        return static::query()
            ->when($publishedOnly, fn (Builder $query) => $query->published())
            ->orderBy('location')
            ->distinct()
            ->pluck('location')
            ->mapWithKeys(fn (string $location) => [Str::slug($location) => $location])
            ->all();
    }
}
