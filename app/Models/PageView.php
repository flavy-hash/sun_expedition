<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Locale;

class PageView extends Model
{
    use MassPrunable;

    public const UPDATED_AT = null;

    protected $fillable = [
        'visitor_hash',
        'path',
        'page_type',
        'tour_id',
        'device',
        'referrer_host',
        'country',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(config('analytics.retention_days')));
    }

    public static function countryLabel(?string $code): ?string
    {
        if (blank($code)) {
            return null;
        }

        // Regional indicator symbols: "TZ" → 🇹🇿
        $flag = implode('', array_map(fn (string $letter) => mb_chr(0x1F1E6 + ord($letter) - ord('A')), str_split(strtoupper($code))));
        $name = Locale::getDisplayRegion('-' . $code, 'en') ?: $code;

        return "{$flag} {$name}";
    }
}
