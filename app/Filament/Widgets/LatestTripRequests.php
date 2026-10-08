<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Inquiries\InquiryResource;
use App\Models\Inquiry;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestTripRequests extends TableWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest trip requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(Inquiry::query()->with('tour')->latest())
            ->recordUrl(fn (Inquiry $record): string => InquiryResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                Action::make('all')->label('View all inquiries')->link()->url(InquiryResource::getUrl()),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25])
            ->emptyStateHeading('No trip requests yet')
            ->emptyStateDescription('Requests from the "Plan my trip" and "Book this adventure" forms will appear here.')
            ->columns([
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
                TextColumn::make('name')->searchable()->weight('medium')
                    ->description(fn (Inquiry $record): ?string => $record->email),
                TextColumn::make('phone')->label('Phone / WhatsApp')->placeholder('—'),
                TextColumn::make('tour.title')->label('Package')->placeholder('Not sure yet')->wrap(),
                TextColumn::make('preferred_dates')->label('Dates')->placeholder('—')->wrap(),
                TextColumn::make('group_size')->label('Group')->placeholder('—'),
                TextColumn::make('message')->limit(60)->tooltip(fn (Inquiry $record): ?string => $record->message)->placeholder('—')->wrap(),
            ]);
    }
}
