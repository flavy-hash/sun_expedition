<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsChartData;
use App\Models\Inquiry;
use Filament\Widgets\ChartWidget;

class TripRequestsChart extends ChartWidget
{
    use BuildsChartData;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Trip requests per month';

    protected ?string $description = '"Plan my trip" and "Book this adventure" form submissions.';

    protected ?string $maxHeight = '280px';

    public ?string $filter = '12';

    protected function getFilters(): ?array
    {
        return ['6' => 'Last 6 months', '12' => 'Last 12 months'];
    }

    protected function getData(): array
    {
        $series = $this->monthlyCounts(Inquiry::query(), (int) $this->filter);

        return [
            'labels' => $series['labels'],
            'datasets' => [$this->barDataset('Trip requests', $series['counts'])],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return $this->baseOptions();
    }
}
