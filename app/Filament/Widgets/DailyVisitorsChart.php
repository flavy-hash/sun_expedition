<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsChartData;
use App\Models\PageView;
use Filament\Widgets\ChartWidget;

class DailyVisitorsChart extends ChartWidget
{
    use BuildsChartData;

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Daily visitors';

    protected ?string $description = 'Unique visitors per day.';

    protected ?string $maxHeight = '260px';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return ['14' => 'Last 14 days', '30' => 'Last 30 days', '90' => 'Last 90 days'];
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $start = now()->subDays($days - 1)->startOfDay();

        $counts = PageView::query()->toBase()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(DISTINCT visitor_hash) as visitors')
            ->groupBy('day')
            ->pluck('visitors', 'day');

        $labels = [];
        $data = [];
        for ($day = $start->copy(); $day <= now(); $day->addDay()) {
            $labels[] = $day->format('M j');
            $data[] = (int) $counts->get($day->toDateString(), 0);
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Visitors',
                'data' => $data,
                'borderColor' => self::BRAND,
                'backgroundColor' => self::BRAND,
                'borderWidth' => 2,
                'pointRadius' => $days > 30 ? 0 : 3,
                'pointHoverRadius' => 5,
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
