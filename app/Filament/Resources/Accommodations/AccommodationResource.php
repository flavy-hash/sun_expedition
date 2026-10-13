<?php

namespace App\Filament\Resources\Accommodations;

use App\Filament\Resources\Accommodations\Pages\ManageAccommodations;
use App\Filament\Support\SiteImageUpload;
use App\Models\Accommodation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class AccommodationResource extends Resource
{
    protected static ?string $model = Accommodation::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static string | UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Accommodation';

    protected static ?string $modelLabel = 'stay';

    protected static ?string $pluralModelLabel = 'accommodation';

    protected static ?string $slug = 'accommodation';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    /** Built-in suggestions plus every area already in use, so admins can pick one or type a new area. */
    public static function locationSuggestions(): array
    {
        return collect(Accommodation::SUGGESTED_LOCATIONS)
            ->merge(Accommodation::query()->distinct()->pluck('location'))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->label('Area')
                    ->required()
                    ->maxLength(100)
                    ->datalist(fn (): array => self::locationSuggestions())
                    ->helperText('Pick a suggestion or type a new area. Each area becomes a filter on the website.'),
                TextInput::make('type')
                    ->maxLength(100)
                    ->datalist(Accommodation::TYPES),
                Select::make('level')
                    ->options(array_combine(Accommodation::LEVELS, Accommodation::LEVELS)),
                Toggle::make('is_published')
                    ->label('Show on website')
                    ->default(true)
                    ->inline(false),
                Textarea::make('description')
                    ->rows(4)
                    ->maxLength(1000)
                    ->columnSpanFull(),
                SiteImageUpload::make('image', 'stays')
                    ->label('Photo')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->disk('site')
                    ->imageHeight(48)
                    ->square(),
                TextColumn::make('name')
                    ->searchable(['name', 'location', 'type'])
                    ->weight('medium')
                    ->description(fn (Accommodation $record): string => collect([$record->type, $record->level])->filter()->join(' · ')),
                TextColumn::make('location')
                    ->label('Area')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                ToggleColumn::make('is_published')
                    ->label('On website'),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->label('Area')
                    ->options(fn (): array => Accommodation::query()->distinct()->orderBy('location')->pluck('location', 'location')->all()),
                SelectFilter::make('level')
                    ->options(array_combine(Accommodation::LEVELS, Accommodation::LEVELS)),
            ])
            ->recordActions([
                Action::make('viewOnSite')
                    ->label('View')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('gray')
                    ->url(fn (Accommodation $record): string => route('accommodations.index', ['location' => $record->location_key]))
                    ->openUrlInNewTab()
                    ->visible(fn (Accommodation $record): bool => $record->is_published),
                EditAction::make()
                    // FileUpload drops values that aren't files on the disk (e.g. an external image URL); keep those when no new photo is uploaded.
                    ->mutateDataUsing(function (array $data, Accommodation $record): array {
                        if (blank($data['image'] ?? null) && Str::startsWith((string) $record->image, ['http://', 'https://'])) {
                            $data['image'] = $record->image;
                        }

                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccommodations::route('/'),
        ];
    }
}
