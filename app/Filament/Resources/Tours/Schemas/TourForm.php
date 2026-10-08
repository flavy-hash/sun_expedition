<?php

namespace App\Filament\Resources\Tours\Schemas;

use App\Filament\Support\SiteImageUpload;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TourForm
{
    public static function configure(Schema $schema, string $category, ?string $circuit): Schema
    {
        return $schema->components([
            Hidden::make('category')->default($category),
            Hidden::make('circuit')->default($circuit),

            Tabs::make('Package')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make('Package')->schema([
                        Section::make()->columns(2)->schema([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                                    if ($operation === 'create') {
                                        $set('slug', Str::slug((string) $state));
                                    }
                                }),
                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->alphaDash()
                                ->unique(ignoreRecord: true)
                                ->helperText('The page address: /tours/this-slug'),
                            TextInput::make('summary')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->helperText('One line shown on package cards and under the page title.'),
                            Textarea::make('description')
                                ->label('Package overview')
                                ->required()
                                ->rows(6)
                                ->columnSpanFull(),
                        ]),
                        Section::make('Hero photo')->schema([
                            SiteImageUpload::make('hero_image', 'tours')
                                ->hiddenLabel()
                                ->helperText('Wide landscape photos work best. Large photos are resized automatically.'),
                        ]),
                    ]),

                    Tab::make('Pricing & facts')->schema([
                        Section::make()->columns(3)->schema([
                            TextInput::make('price_from')
                                ->label('Price from (per person)')
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->prefix('$'),
                            TextInput::make('duration_days')
                                ->label('Duration (days)')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(60),
                            self::levelField($category),
                            TextInput::make('group_size_max')
                                ->label('Max group size')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(255),
                            TextInput::make('best_time')
                                ->label('Best time to go')
                                ->placeholder('e.g. June – October')
                                ->maxLength(255),
                            TextInput::make('location')
                                ->maxLength(255),
                            TextInput::make('highlight_stat')
                                ->label('Card badge')
                                ->placeholder('e.g. 90% summit rate')
                                ->maxLength(255),
                            TextInput::make('rating')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(5)
                                ->step(0.1),
                            TextInput::make('reviews_count')
                                ->label('Number of reviews')
                                ->numeric()
                                ->minValue(0),
                        ]),
                        Section::make('Visibility')->columns(2)->schema([
                            Toggle::make('is_featured')
                                ->label('Feature on the homepage')
                                ->inline(false),
                            TextInput::make('sort_order')
                                ->label('Sort order')
                                ->numeric()
                                ->default(0)
                                ->helperText('Lower numbers are listed first. You can also drag rows in the list.'),
                        ]),
                    ]),

                    Tab::make('Itinerary')->schema([
                        self::itineraryRepeater(),
                    ]),

                    Tab::make('Highlights & inclusions')->schema([
                        self::listRepeater('highlights', 'Highlights', 'Add highlight'),
                        self::listRepeater('included', "What's included", 'Add item'),
                        self::listRepeater('excluded', 'Not included', 'Add item'),
                    ]),
                ]),
        ]);
    }

    // The `difficulty` column doubles as the safari tier (drives the Safari menu filters) and the Zanzibar trip style.
    private static function levelField(string $category): Field
    {
        return match ($category) {
            'safari' => Select::make('difficulty')
                ->label('Tier')
                ->options(['Budget' => 'Budget', 'Classic' => 'Classic', 'Mid-range' => 'Mid-range', 'Luxury' => 'Luxury'])
                ->helperText('Appears as a filter in the Safari menu.'),
            'kilimanjaro' => TextInput::make('difficulty')
                ->label('Difficulty')
                ->datalist(['Moderate', 'Challenging', 'Demanding'])
                ->maxLength(255),
            default => TextInput::make('difficulty')
                ->label('Trip style')
                ->datalist(['Relaxed', 'Cultural', 'Adventure'])
                ->maxLength(255),
        };
    }

    private static function listRepeater(string $name, string $label, string $addLabel): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->simple(TextInput::make('item')->required()->maxLength(255))
            ->defaultItems(0)
            ->addActionLabel($addLabel)
            ->columnSpanFull();
    }

    private static function itineraryRepeater(): Repeater
    {
        return Repeater::make('itineraryItems')
            ->label('Day-by-day itinerary')
            ->relationship()
            ->orderColumn('day_number')
            ->itemLabel(fn (array $state, ?int $index): string => collect([
                $index === null ? null : 'Day ' . ($index + 1),
                $state['title'] ?? null,
            ])->filter()->join(' · '))
            ->collapsible()
            ->defaultItems(0)
            ->addActionLabel('Add day')
            ->columnSpanFull()
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::withoutEmptyBlocks($data))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::withoutEmptyBlocks($data))
            ->schema([
                TextInput::make('title')
                    ->label('Day title')
                    ->placeholder('e.g. Arrival, Stone Town')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Grid::make(2)->columnSpanFull()->schema([
                    SiteImageUpload::make('image', 'itinerary')->label('Day photo'),
                    Grid::make(1)->schema([
                        TextInput::make('image_caption')
                            ->label('Photo caption')
                            ->placeholder('e.g. Stone Town')
                            ->maxLength(255),
                        Textarea::make('note')
                            ->rows(2)
                            ->helperText('Optional small print under the description, e.g. check-in times.'),
                    ]),
                ]),
                Fieldset::make('Activity (optional)')
                    ->statePath('activity')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('label')
                            ->label('Activity name')
                            ->placeholder('e.g. Spice farm tour')
                            ->maxLength(255),
                        Toggle::make('optional')
                            ->label('Optional extra, paid separately')
                            ->inline(false),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        SiteImageUpload::make('image', 'itinerary')
                            ->label('Activity photo')
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Overnight & meals')
                    ->statePath('accommodation')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('location')
                            ->label('Overnight location')
                            ->placeholder('e.g. Nungwi, or Barafu Camp')
                            ->helperText('Leave empty on the final day.')
                            ->maxLength(255),
                        TextInput::make('meal_plan')
                            ->label('Meal plan')
                            ->datalist(['Full board', 'Half board', 'Bed and breakfast', 'Breakfast only', 'Lunch included'])
                            ->maxLength(255),
                        Repeater::make('tiers')
                            ->label('Accommodation options')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Package')
                                    ->datalist(['Comfort', 'Premium'])
                                    ->required()
                                    ->maxLength(50),
                                TextInput::make('name')
                                    ->label('Where guests stay')
                                    ->placeholder('e.g. 4-star beach resort, Nungwi')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add option')
                            ->columnSpanFull(),
                        SiteImageUpload::make('image', 'itinerary')->label('Accommodation photo'),
                        TextInput::make('image_caption')
                            ->label('Accommodation photo caption')
                            ->maxLength(255),
                    ]),
            ]);
    }

    // Untouched optional fieldsets still submit empty keys; store null so the public page skips them.
    private static function withoutEmptyBlocks(array $data): array
    {
        if (blank($data['activity']['label'] ?? null) && blank($data['activity']['description'] ?? null)) {
            $data['activity'] = null;
        }

        if (blank($data['accommodation']['location'] ?? null) && blank($data['accommodation']['meal_plan'] ?? null)) {
            $data['accommodation'] = null;
        }

        return $data;
    }
}
