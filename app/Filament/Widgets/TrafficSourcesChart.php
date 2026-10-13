<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsChartData;
use App\Models\PageView;
use Filament\Widgets\ChartWidget;

class TrafficSourcesChart extends ChartWidget
{
    use BuildsChartData;

    protected static ?int $sort = 5;

    protected ?string $heading = 'Where visitors come from';

    protected ?string $description = 'Visitors by referring site, last 30 days (top 6).';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        // One visitor counted once per source per day, so a visitor browsing ten pages isn't counted ten times.
        $sources = PageView::query()->toBase()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw("COALESCE(referrer_host, 'Direct / unknown') as source, COUNT(DISTINCT visitor_hash) as visitors")
            ->groupBy('source')
            ->orderByDesc('visitors')
            ->limit(6)
            ->pluck('visitors', 'source');

        return [
            'labels' => $sources->keys()->all(),
            'datasets' => [$this->barDataset('Visitors', $sources->values()->map(fn ($v) => (int) $v)->all())],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return $this->baseOptions(horizontal: true);
    }
}
