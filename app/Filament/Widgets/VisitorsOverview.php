<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PageViews\PageViewResource;
use App\Models\PageView;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitorsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Website visitors';

    protected ?string $description = 'Anonymous, cookie-free statistics. A "visitor" is one device on one day.';

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $weekAgo = now()->subDays(6)->startOfDay();

        // The visitor hash rotates daily, so unique visitors over a week = sum of each day's unique visitors.
        $daily = PageView::query()->toBase()
            ->where('created_at', '>=', $weekAgo)
            ->selectRaw('DATE(created_at) as day, COUNT(DISTINCT visitor_hash) as visitors, COUNT(*) as views')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $series = collect(range(6, 0))->map(fn (int $daysAgo) => $daily->get(now()->subDays($daysAgo)->toDateString()));
        $visitorsToday = (int) ($daily->get($today->toDateString())->visitors ?? 0);
        $viewsToday = (int) ($daily->get($today->toDateString())->views ?? 0);

        return [
            Stat::make('Visitors today', $visitorsToday)
                ->chart($series->map(fn ($day) => (int) ($day->visitors ?? 0))->all())
                ->color('primary')
                ->url(PageViewResource::getUrl()),
            Stat::make('Page views today', $viewsToday)
                ->description($visitorsToday ? round($viewsToday / $visitorsToday, 1) . ' pages per visitor' : 'No visits yet today')
                ->descriptionIcon(Heroicon::OutlinedDocumentText),
            Stat::make('Visitors · last 7 days', (int) $series->sum(fn ($day) => $day->visitors ?? 0))
                ->description('Including today'),
            Stat::make('Page views · last 7 days', (int) $series->sum(fn ($day) => $day->views ?? 0))
                ->description('Including today'),
        ];
    }
}
