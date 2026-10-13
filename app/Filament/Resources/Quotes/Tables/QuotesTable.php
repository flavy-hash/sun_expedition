<?php

namespace App\Filament\Resources\Quotes\Tables;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Quote;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable(['customer_name', 'customer_email'])
                    ->weight('medium')
                    ->description(fn (Quote $record): string => $record->customer_email),
                TextColumn::make('tour.title')
                    ->label('Package')
                    ->placeholder('Tailor-made')
                    ->wrap(),
                TextColumn::make('total')
                    ->state(fn (Quote $record): string => Quote::money($record->total))
                    ->alignEnd(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Quote::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => QuoteResource::statusColor($state)),
                TextColumn::make('source')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'website' ? 'Website' : 'Admin')
                    ->color(fn (string $state): string => $state === 'website' ? 'primary' : 'gray'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Quote::STATUSES),
                SelectFilter::make('source')->options(['admin' => 'Admin', 'website' => 'Website']),
            ])
            ->recordActions([
                QuoteResource::downloadAction()->iconButton()->tooltip('Download PDF'),
                ActionGroup::make([
                    EditAction::make(),
                    QuoteResource::emailAction(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
