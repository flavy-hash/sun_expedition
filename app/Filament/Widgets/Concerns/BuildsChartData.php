<?php

namespace App\Filament\Widgets\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait BuildsChartData
{
    protected const BRAND = '#d9600f';

    /**
     * Counts rows per calendar month for the last $months months (oldest first), including empty months.
     *
     * @return array{labels: list<string>, counts: list<int>}
     */
    protected function monthlyCounts(Builder $query, int $months): array
    {
        $start = now()->startOfMonth()->subMonths($months - 1);

        // Grouped in PHP so it behaves the same on MySQL and the SQLite test database.
        $perMonth = $query->where('created_at', '>=', $start)->pluck('created_at')
            ->countBy(fn (Carbon $date): string => $date->format('Y-m'));

        $labels = [];
        $counts = [];
        for ($month = $start->copy(); $month <= now(); $month->addMonth()) {
            $labels[] = $month->format('M Y');
            $counts[] = $perMonth->get($month->format('Y-m'), 0);
        }

        return ['labels' => $labels, 'counts' => $counts];
    }

    // Recessive grid, whole-number ticks, no legend: every chart here is a single series named by its heading.
    protected function baseOptions(bool $horizontal = false): array
    {
        $valueAxis = ['beginAtZero' => true, 'ticks' => ['precision' => 0], 'grid' => ['color' => 'rgba(127,127,127,0.15)'], 'border' => ['display' => false]];
        $categoryAxis = ['grid' => ['display' => false], 'border' => ['display' => false]];

        return [
            'indexAxis' => $horizontal ? 'y' : 'x',
            'plugins' => ['legend' => ['display' => false]],
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'scales' => $horizontal
                ? ['x' => $valueAxis, 'y' => $categoryAxis]
                : ['x' => $categoryAxis, 'y' => $valueAxis],
        ];
    }

    protected function barDataset(string $label, array $data): array
    {
        return [
            'label' => $label,
            'data' => $data,
            'backgroundColor' => self::BRAND,
            'hoverBackgroundColor' => '#b34e0b',
            'borderWidth' => 0,
            'borderRadius' => 4,
            'borderSkipped' => 'start',
            'maxBarThickness' => 32,
        ];
    }
}
