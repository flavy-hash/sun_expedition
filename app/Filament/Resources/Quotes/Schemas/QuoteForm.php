<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Filament\Pages\SiteSettings;
use App\Models\Quote;
use App\Models\Setting;
use App\Models\Tour;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Customer')
                    ->columns(2)
                    ->schema([
                        TextInput::make('customer_name')->label('Name')->required()->maxLength(255),
                        TextInput::make('customer_email')->label('Email')->email()->required()->maxLength(255),
                        TextInput::make('customer_phone')->label('Phone / WhatsApp')->tel()->maxLength(50),
                        TextInput::make('customer_country')->label('Country')->maxLength(100),
                    ]),

                Section::make('Quote')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reference')
                            ->state(fn (?Quote $record): string => $record?->reference ?? 'Assigned when saved')
                            ->fontFamily('mono'),
                        Select::make('status')
                            ->options(Quote::STATUSES)
                            ->default('draft')
                            ->required(),
                        DatePicker::make('valid_until')
                            ->label('Valid until')
                            ->default(fn () => now()->addDays(config('quotes.validity_days')))
                            ->native(false),
                        Toggle::make('show_bank_details')
                            ->label('Show bank details on the PDF')
                            ->default(true)
                            ->inline(false)
                            ->helperText(fn (): HtmlString => self::bankAccountsHelp()),
                    ]),

                Section::make('Trip')
                    ->columns(2)
                    ->schema([
                        Select::make('tour_id')
                            ->label('Package')
                            ->relationship('tour', 'title')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::fillItemsFromPackage($get, $set))
                            ->helperText('When the cost list is empty, picking a package fills it from the package price.')
                            ->columnSpanFull(),
                        TextInput::make('adults')->numeric()->minValue(0)->maxValue(100)->default(2)->required()->live(onBlur: true),
                        TextInput::make('children')->numeric()->minValue(0)->maxValue(100)->default(0)->required()->live(onBlur: true),
                        DatePicker::make('travel_start')
                            ->label('Start date')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (?string $state, Get $get, Set $set): void {
                                $tour = Tour::find($get('tour_id'));
                                if ($state && $tour && blank($get('travel_end'))) {
                                    $set('travel_end', Carbon::parse($state)->addDays($tour->duration_days - 1)->toDateString());
                                }
                            }),
                        DatePicker::make('travel_end')
                            ->label('End date')
                            ->native(false)
                            ->afterOrEqual('travel_start'),
                    ]),

                Section::make('Totals')
                    ->columns(3)
                    ->schema([
                        TextInput::make('discount')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix(config('quotes.currency_symbol'))
                            ->live(onBlur: true),
                        TextEntry::make('subtotal_preview')
                            ->label('Subtotal')
                            ->state(fn (Get $get): string => Quote::money(Quote::itemsSubtotal($get('items') ?? []))),
                        TextEntry::make('total_preview')
                            ->label('Total')
                            ->weight('bold')
                            ->state(fn (Get $get): string => Quote::money(max(0, Quote::itemsSubtotal($get('items') ?? []) - (float) $get('discount')))),
                    ]),

                Section::make('Itemised costs')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('items')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('description')->required()->maxLength(255)->columnSpan(2),
                                TextInput::make('quantity')->numeric()->minValue(0)->default(1)->required()->live(onBlur: true),
                                TextInput::make('unit_price')
                                    ->label('Unit price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix(config('quotes.currency_symbol'))
                                    ->required()
                                    ->live(onBlur: true),
                            ])
                            ->columns(4)
                            ->live()
                            ->defaultItems(0)
                            ->reorderable()
                            ->addActionLabel('Add line'),
                    ]),

                Section::make('Notes')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('notes')->hiddenLabel()->rows(4)->maxLength(5000)
                            ->helperText('Printed on the PDF under the totals.'),
                    ]),
            ]);
    }

    /** Adults × price and children × price × child rate, keyed the way the repeater expects. */
    public static function packageItemsState(Tour $tour, int $adults, int $children): array
    {
        return collect(Quote::packageItems($tour, $adults, $children))
            ->mapWithKeys(fn (array $item) => [(string) Str::uuid() => $item])
            ->all();
    }

    private static function fillItemsFromPackage(Get $get, Set $set): void
    {
        $tour = Tour::find($get('tour_id'));
        if (! $tour || filled($get('items'))) {
            return;
        }

        $set('items', self::packageItemsState($tour, (int) $get('adults'), (int) $get('children')));
    }

    private static function bankAccountsHelp(): HtmlString
    {
        $accounts = Setting::bankAccounts();
        $link = '<a href="' . e(SiteSettings::getUrl()) . '" class="underline">Site settings</a>';

        if ($accounts === []) {
            return new HtmlString("No bank accounts are set up yet, so nothing will be printed. Add them in {$link}.");
        }

        $names = collect($accounts)->map(fn (array $account) => e($account['bank_name'] . (filled($account['currency'] ?? null) ? " ({$account['currency']})" : '')))->join(', ');

        return new HtmlString("Prints: {$names}. Manage accounts in {$link}.");
    }
}
