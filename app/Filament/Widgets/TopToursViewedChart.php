<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsChartData;
use App\Models\PageView;
use App\Models\Tour;
use Filament\Widgets\ChartWidget;

class TopToursViewedChart extends ChartWidget
{
    use BuildsChartData;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Most viewed packages';

    protected ?string $description = 'Package page views, last 30 days (top 6).';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $views = PageView::query()->toBase()
            ->whereNotNull('tour_id')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('tour_id, COUNT(*) as views')
            ->groupBy('tour_id')
            ->orderByDesc('views')
            ->limit(6)
            ->pluck('views', 'tour_id');

        $titles = Tour::whereIn('id', $views->keys())->pluck('title', 'id');

        return [
            'labels' => $views->keys()->map(fn ($id) => $titles[$id] ?? 'Deleted package')->all(),
            'datasets' => [$this->barDataset('Views', $views->values()->map(fn ($v) => (int) $v)->all())],
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
