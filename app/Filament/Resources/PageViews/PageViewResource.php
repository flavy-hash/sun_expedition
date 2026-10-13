<?php

namespace App\Filament\Resources\PageViews;

use App\Filament\Resources\PageViews\Pages\ListPageViews;
use App\Models\PageView;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use UnitEnum;

class PageViewResource extends Resource
{
    protected static ?string $model = PageView::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static string | UnitEnum | null $navigationGroup = 'Insights';

    protected static ?string $navigationLabel = 'Visitor activity';

    protected static ?string $modelLabel = 'page view';

    protected static ?string $pluralModelLabel = 'visitor activity';

    protected static ?string $slug = 'visitor-activity';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description(new HtmlString(e(sprintf(
                'Anonymous statistics: page, package viewed, device type, country and the site a visitor came from. No cookies, no IP addresses, no names. '
                . 'The visitor ID is a code that changes every day, so it only groups one visitor\'s pages on one day. Visitors who send "Do Not Track" or '
                . '"Global Privacy Control", bots and signed-in staff are never recorded. Entries are deleted after %d days.',
                config('analytics.retention_days'),
            )) . ' IP geolocation by <a href="https://db-ip.com" target="_blank" rel="noopener" class="underline">DB-IP</a>.'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M j, H:i')
                    ->sortable(),
                TextColumn::make('page_type')
                    ->label('Page')
                    ->badge()
                    ->color('gray')
                    ->description(fn (PageView $record): string => $record->path),
                TextColumn::make('tour.title')
                    ->label('Package viewed')
                    ->placeholder('—')
                    ->wrap(),
                IconColumn::make('device')
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'mobile' => Heroicon::OutlinedDevicePhoneMobile,
                        'tablet' => Heroicon::OutlinedDeviceTablet,
                        default => Heroicon::OutlinedComputerDesktop,
                    })
                    ->tooltip(fn (string $state): string => ucfirst($state))
                    ->color('gray'),
                TextColumn::make('country')
                    ->formatStateUsing(fn (?string $state): ?string => PageView::countryLabel($state))
                    ->placeholder('Unknown'),
                TextColumn::make('referrer_host')
                    ->label('Came from')
                    ->placeholder('Direct / unknown'),
                TextColumn::make('visitor_hash')
                    ->label('Visitor ID (daily)')
                    ->fontFamily('mono')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('date')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->where('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->where('created_at', '<', now()->parse($date)->addDay())))
                    ->indicateUsing(fn (array $data): array => array_filter([
                        filled($data['from'] ?? null) ? 'From ' . $data['from'] : null,
                        filled($data['until'] ?? null) ? 'Until ' . $data['until'] : null,
                    ])),
                SelectFilter::make('page_type')
                    ->label('Page')
                    ->options(fn (): array => PageView::query()->distinct()->orderBy('page_type')->pluck('page_type', 'page_type')->all()),
                SelectFilter::make('tour')
                    ->label('Package')
                    ->relationship('tour', 'title')
                    ->searchable(),
                SelectFilter::make('device')
                    ->options(['mobile' => 'Mobile', 'tablet' => 'Tablet', 'desktop' => 'Desktop']),
                SelectFilter::make('country')
                    ->options(fn (): array => PageView::query()->whereNotNull('country')->distinct()->pluck('country')
                        ->mapWithKeys(fn (string $code) => [$code => PageView::countryLabel($code)])->sort()->all())
                    ->searchable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageViews::route('/'),
        ];
    }
}
