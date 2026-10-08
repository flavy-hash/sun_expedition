<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsChartData;
use App\Models\Tour;
use Filament\Widgets\ChartWidget;

class PopularPackagesChart extends ChartWidget
{
    use BuildsChartData;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Most requested packages';

    protected ?string $description = 'Trip requests per package, all time (top 6).';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $tours = Tour::whereHas('inquiries')
            ->withCount('inquiries')
            ->orderByDesc('inquiries_count')
            ->take(6)
            ->get(['id', 'title']);

        return [
            'labels' => $tours->pluck('title')->all(),
            'datasets' => [$this->barDataset('Trip requests', $tours->pluck('inquiries_count')->all())],
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
