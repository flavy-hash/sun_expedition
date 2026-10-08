<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsChartData;
use App\Models\NewsletterSubscriber;
use Filament\Widgets\ChartWidget;

class SubscribersChart extends ChartWidget
{
    use BuildsChartData;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Newsletter signups per month';

    protected ?string $description = 'New subscribers over the last 12 months.';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $series = $this->monthlyCounts(NewsletterSubscriber::query(), 12);

        return [
            'labels' => $series['labels'],
            'datasets' => [[
                'label' => 'New subscribers',
                'data' => $series['counts'],
                'borderColor' => self::BRAND,
                'backgroundColor' => self::BRAND,
                'borderWidth' => 2,
                'pointRadius' => 4,
                'pointHoverRadius' => 6,
                'tension' => 0,
                'fill' => false,
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return $this->baseOptions();
    }
}
