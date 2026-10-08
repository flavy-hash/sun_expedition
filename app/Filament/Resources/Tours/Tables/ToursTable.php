<?php

namespace App\Filament\Resources\Tours\Tables;

use App\Models\Tour;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ToursTable
{
    public static function configure(Table $table, string $category): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('hero_image')
                    ->label('')
                    ->disk('site')
                    ->imageHeight(48)
                    ->square(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Tour $record): string => $record->summary)
                    ->wrap(),
                TextColumn::make('duration_days')
                    ->label('Days')
                    ->sortable(),
                TextColumn::make('price_from')
                    ->label('From')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('difficulty')
                    ->label(match ($category) {
                        'safari' => 'Tier',
                        'kilimanjaro' => 'Difficulty',
                        default => 'Style',
                    })
                    ->badge(),
                TextColumn::make('itinerary_items_count')
                    ->label('Itinerary days')
                    ->counts('itineraryItems'),
                ToggleColumn::make('is_featured')
                    ->label('Homepage'),
            ])
            ->recordActions([
                Action::make('viewOnSite')
                    ->label('View')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('gray')
                    ->url(fn (Tour $record): string => route('tours.show', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
