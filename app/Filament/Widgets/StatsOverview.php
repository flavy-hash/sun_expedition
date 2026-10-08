<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Inquiries\InquiryResource;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Inquiry;
use App\Models\NewsletterSubscriber;
use App\Models\Testimonial;
use App\Models\Tour;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            $this->tripRequests(),
            $this->pendingReviews(),
            $this->subscribers(),
            $this->packages(),
        ];
    }

    private function tripRequests(): Stat
    {
        $current = Inquiry::where('created_at', '>=', now()->subDays(30))->count();
        $previous = Inquiry::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
        $change = $current - $previous;

        $daily = Inquiry::where('created_at', '>=', now()->subDays(13)->startOfDay())->pluck('created_at')
            ->countBy(fn ($date) => $date->toDateString());
        $sparkline = collect(range(13, 0))->map(fn (int $daysAgo) => $daily->get(now()->subDays($daysAgo)->toDateString(), 0))->all();

        return Stat::make('Trip requests · last 30 days', $current)
            ->description(match (true) {
                $change > 0 => "{$change} more than the previous 30 days",
                $change < 0 => abs($change) . ' fewer than the previous 30 days',
                default => 'Same as the previous 30 days',
            })
            ->descriptionIcon($change >= 0 ? Heroicon::ArrowTrendingUp : Heroicon::ArrowTrendingDown)
            ->chart($sparkline)
            ->color('primary')
            ->url(InquiryResource::getUrl());
    }

    private function pendingReviews(): Stat
    {
        $pending = Testimonial::where('is_approved', false)->count();

        return Stat::make('Reviews awaiting approval', $pending)
            ->description($pending ? 'Click to review and publish' : 'All guest reviews handled')
            ->descriptionIcon($pending ? Heroicon::ExclamationTriangle : Heroicon::CheckCircle)
            ->color($pending ? 'warning' : 'success')
            ->url(ReviewResource::getUrl());
    }

    private function subscribers(): Stat
    {
        $thisMonth = NewsletterSubscriber::where('created_at', '>=', now()->startOfMonth())->count();

        return Stat::make('Newsletter subscribers', NewsletterSubscriber::count())
            ->description("{$thisMonth} new this month")
            ->descriptionIcon(Heroicon::Envelope);
    }

    private function packages(): Stat
    {
        $byCategory = Tour::query()->toBase()->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category');

        return Stat::make('Live packages', $byCategory->sum())
            ->description(sprintf('%d safaris · %d Kilimanjaro · %d Zanzibar',
                $byCategory->get('safari', 0), $byCategory->get('kilimanjaro', 0), $byCategory->get('zanzibar', 0)))
            ->descriptionIcon(Heroicon::Map);
    }
}
