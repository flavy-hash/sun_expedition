<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Filament\Resources\Inquiries\InquiryResource;
use App\Models\Inquiry;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    public function getTabs(): array
    {
        $counts = Inquiry::query()->toBase()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $tabs = collect(InquiryResource::STATUSES)->map(fn (string $label, string $status) => Tab::make($label)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status))
            ->badge($counts->get($status) ?: null)
            ->badgeColor(InquiryResource::statusColor($status)))
            ->all();

        return $tabs + ['all' => Tab::make('All')];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return Inquiry::where('status', 'new')->exists() ? 'new' : 'all';
    }
}
