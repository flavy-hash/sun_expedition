<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    public const STATUSES = ['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted', 'declined' => 'Declined'];

    protected $fillable = [
        'source',
        'status',
        'tour_id',
        'inquiry_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_country',
        'adults',
        'children',
        'travel_start',
        'travel_end',
        'items',
        'discount',
        'notes',
        'show_bank_details',
        'valid_until',
    ];

    protected $casts = [
        'items' => 'array',
        'discount' => 'decimal:2',
        'show_bank_details' => 'boolean',
        'travel_start' => 'date',
        'travel_end' => 'date',
        'valid_until' => 'date',
    ];

    protected static function booted(): void
    {
        // The reference comes from the id, so it can only be set once the row exists.
        static::created(function (Quote $quote): void {
            $quote->reference = sprintf('QT-%s-%06d', $quote->created_at->format('Y'), $quote->id);
            $quote->saveQuietly();
        });
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    protected function subtotal(): Attribute
    {
        return Attribute::get(fn (): float => self::itemsSubtotal($this->items ?? []));
    }

    protected function total(): Attribute
    {
        return Attribute::get(fn (): float => max(0, $this->subtotal - (float) $this->discount));
    }

    public static function itemsSubtotal(array $items): float
    {
        return round(collect($items)->sum(fn ($item) => (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0)), 2);
    }

    /** Package price lines: adults at the package price, children at the configured child rate. */
    public static function packageItems(Tour $tour, int $adults, int $children): array
    {
        $items = [];
        if ($adults > 0) {
            $items[] = ['description' => "{$tour->title} — adult", 'quantity' => $adults, 'unit_price' => (float) $tour->price_from];
        }
        if ($children > 0) {
            $items[] = ['description' => "{$tour->title} — child", 'quantity' => $children, 'unit_price' => round($tour->price_from * config('quotes.child_rate'), 2)];
        }

        return $items;
    }

    public static function money(float|int|string|null $amount): string
    {
        return config('quotes.currency_symbol') . number_format((float) $amount, 2);
    }

    public function pdfFilename(): string
    {
        return "Suni-Expedition-Quote-{$this->reference}.pdf";
    }

    public function travellersLabel(): string
    {
        return collect([
            $this->adults ? trans_choice(':count adult|:count adults', $this->adults) : null,
            $this->children ? trans_choice(':count child|:count children', $this->children) : null,
        ])->filter()->join(', ') ?: '—';
    }
}
