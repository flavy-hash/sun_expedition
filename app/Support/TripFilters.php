<?php

namespace App\Support;

use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Destination / style / duration filters for the all-trips page (/tours?destination=…&style=…&days=…).
 * Each row only offers values that exist within the rows above it, and unknown values are ignored,
 * so every chip leads to at least one trip.
 */
class TripFilters
{
    private const DESTINATIONS = [
        'safari' => ['label' => 'Safaris', 'category' => 'safari'],
        'northern' => ['label' => 'Northern Circuit', 'category' => 'safari', 'circuit' => 'northern'],
        'southern' => ['label' => 'Southern Circuit', 'category' => 'safari', 'circuit' => 'southern'],
        'kilimanjaro' => ['label' => 'Kilimanjaro', 'category' => 'kilimanjaro'],
        'zanzibar' => ['label' => 'Zanzibar', 'category' => 'zanzibar'],
        'day-trips' => ['label' => 'Day trips', 'category' => 'day-trips'],
    ];

    // Style row order per category. Safari tiers are also what "All destinations" offers.
    private const STYLE_ORDER = [
        'safari' => ['Budget', 'Classic', 'Mid-range', 'Luxury'],
        'kilimanjaro' => ['Moderate', 'Challenging', 'Demanding'],
        'day-trips' => ['Easy', 'Moderate', 'Strenuous'],
    ];

    private Collection $all;

    private ?string $destination;

    private ?string $style;

    private ?int $days;

    public function __construct(private Request $request)
    {
        $this->all = Tour::orderBy('sort_order')->orderBy('title')->get();

        $this->destination = $this->pick($request->query('destination'), array_keys($this->destinationOptions()));
        $this->style = $this->pick($request->query('style'), array_keys($this->styleOptions()));
        $days = $request->query('days');
        $this->days = is_string($days) && in_array((int) $days, $this->durationOptions(), true) ? (int) $days : null;
    }

    /** Trips matching every active filter. */
    public function tours(): Collection
    {
        return $this->inDuration($this->inStyle($this->inDestination($this->all)))->values();
    }

    public function activeCount(): int
    {
        return count(array_filter([$this->destination, $this->style, $this->days]));
    }

    /** @return list<array{label: string, links: list<array{label: string, url: string, active: bool}>}> */
    public function rows(): array
    {
        $rows = [[
            'label' => __('Destination'),
            'links' => $this->links(__('All'), $this->destinationOptions(), $this->destination, fn (?string $key) => ['destination' => $key]),
        ]];

        $styles = $this->styleOptions();
        if (count($styles) > 1 || $this->style) {
            $category = $this->destination ? self::DESTINATIONS[$this->destination]['category'] : 'safari';
            $rows[] = [
                'label' => in_array($category, ['kilimanjaro', 'day-trips'], true) ? __('Difficulty') : __('Style'),
                'links' => $this->links(__('All'), $styles, $this->style, fn (?string $key) => ['destination' => $this->destination, 'style' => $key, 'days' => $this->days]),
            ];
        }

        $durations = collect($this->durationOptions())->mapWithKeys(fn (int $days) => [$days => trans_choice(':count day|:count days', $days)])->all();
        if (count($durations) > 1 || $this->days) {
            $rows[] = [
                'label' => __('Duration'),
                'links' => $this->links(__('Any'), $durations, $this->days, fn ($key) => ['destination' => $this->destination, 'style' => $this->style, 'days' => $key]),
            ];
        }

        return $rows;
    }

    // ---- Options --------------------------------------------------------------------------------------------

    private function destinationOptions(): array
    {
        return collect(self::DESTINATIONS)
            ->filter(fn (array $destination, string $key) => $this->inDestination($this->all, $key)->isNotEmpty())
            ->map(fn (array $destination) => __($destination['label']))
            ->all();
    }

    /** slug => label, for the styles used by trips in the selected destination (safari tiers when none). */
    private function styleOptions(): array
    {
        $category = $this->destination ? self::DESTINATIONS[$this->destination]['category'] : 'safari';
        $pool = $this->destination ? $this->inDestination($this->all) : $this->all;
        $order = $category === 'zanzibar' ? array_keys(config('zanzibar.styles', [])) : (self::STYLE_ORDER[$category] ?? []);

        $values = $pool->pluck('difficulty')->filter()->unique();
        if (! $this->destination) {
            $values = $values->intersect($order);
        }

        return $values
            ->sortBy(fn (string $value) => ($index = array_search($value, $order, true)) === false ? PHP_INT_MAX : $index)
            ->mapWithKeys(fn (string $value) => [Str::slug($value) => __($category === 'zanzibar' ? (config('zanzibar.styles', [])[$value]['label'] ?? $value) : $value)])
            ->all();
    }

    /** @return list<int> trip lengths within the selected destination and style */
    private function durationOptions(): array
    {
        return $this->inStyle($this->inDestination($this->all))->pluck('duration_days')->unique()->sort()->values()->all();
    }

    // ---- Filtering ------------------------------------------------------------------------------------------

    private function inDestination(Collection $tours, ?string $key = null): Collection
    {
        $destination = self::DESTINATIONS[$key ?? $this->destination ?? ''] ?? null;

        return $destination
            ? $tours->filter(fn (Tour $tour) => $tour->category === $destination['category'] && (! isset($destination['circuit']) || $tour->circuit === $destination['circuit']))
            : $tours;
    }

    private function inStyle(Collection $tours): Collection
    {
        return $this->style ? $tours->filter(fn (Tour $tour) => $tour->difficulty && Str::slug($tour->difficulty) === $this->style) : $tours;
    }

    private function inDuration(Collection $tours): Collection
    {
        return $this->days ? $tours->where('duration_days', $this->days) : $tours;
    }

    // ---- Helpers --------------------------------------------------------------------------------------------

    private function pick(mixed $value, array $allowed): ?string
    {
        return is_string($value) && in_array($value, $allowed, true) ? $value : null;
    }

    private function links(string $allLabel, array $options, string|int|null $active, \Closure $params): array
    {
        $links = [['label' => $allLabel, 'url' => $this->url($params(null)), 'active' => $active === null]];

        foreach ($options as $key => $label) {
            $links[] = ['label' => $label, 'url' => $this->url($params($key)), 'active' => $active !== null && (string) $active === (string) $key];
        }

        return $links;
    }

    private function url(array $params): string
    {
        return route('tours.index', array_filter($params, fn ($value) => $value !== null && $value !== '')) . '#tours-grid';
    }
}
