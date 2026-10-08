<?php

namespace App\Filament\Resources\Reviews;

use App\Filament\Resources\Reviews\Pages\CreateReview;
use App\Filament\Resources\Reviews\Pages\EditReview;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class ReviewResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedStar;

    protected static string | UnitEnum | null $navigationGroup = 'Guests';

    protected static ?string $navigationLabel = 'Reviews';

    protected static ?string $modelLabel = 'review';

    protected static ?string $slug = 'reviews';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationBadge(): ?string
    {
        $pending = Testimonial::where('is_approved', false)->count();

        return $pending ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Awaiting approval';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(100),
                    TextInput::make('email')->email()->maxLength(255)->helperText('Never shown on the site.'),
                    TextInput::make('country')->maxLength(100),
                    Select::make('tour_id')
                        ->label('Package')
                        ->relationship('tour', 'title')
                        ->searchable()
                        ->preload(),
                    Select::make('rating')
                        ->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★'])
                        ->default(5)
                        ->required(),
                    Toggle::make('is_approved')
                        ->label('Published on the site')
                        ->default(true)
                        ->inline(false),
                    TextInput::make('title')->required()->maxLength(120)->columnSpanFull(),
                    Textarea::make('quote')->label('Review')->required()->rows(6)->columnSpanFull(),
                    FileUpload::make('images')
                        ->label('Photos')
                        ->image()
                        ->multiple()
                        ->maxFiles(4)
                        ->reorderable()
                        ->disk('public')
                        ->directory('reviews')
                        ->visibility('public')
                        ->maxSize(10240)
                        ->imageResizeMode('contain')
                        ->imageResizeTargetWidth('1600')
                        ->imageResizeTargetHeight('1600')
                        ->imageResizeUpscale(false)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('images')
                    ->label('Photos')
                    ->disk('public')
                    ->imageHeight(44)
                    ->square()
                    ->stacked()
                    ->limit(2)
                    ->limitedRemainingText(),
                TextColumn::make('title')
                    ->weight('medium')
                    ->searchable(['title', 'quote'])
                    ->description(fn (Testimonial $record): string => str($record->quote)->limit(90))
                    ->wrap(),
                TextColumn::make('name')
                    ->label('Guest')
                    ->searchable()
                    ->description(fn (Testimonial $record): ?string => $record->country),
                TextColumn::make('tour.title')
                    ->label('Package')
                    ->placeholder('General')
                    ->wrap(),
                TextColumn::make('rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('is_approved')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Published' : 'Pending')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tour')->label('Package')->relationship('tour', 'title'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Testimonial $record): bool => ! $record->is_approved)
                    ->action(function (Testimonial $record): void {
                        $record->update(['is_approved' => true]);
                        Notification::make()->title('Review published')->success()->send();
                    }),
                Action::make('unpublish')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->color('gray')
                    ->visible(fn (Testimonial $record): bool => $record->is_approved)
                    ->action(function (Testimonial $record): void {
                        $record->update(['is_approved' => false]);
                        Notification::make()->title('Review hidden from the site')->success()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label('Publish selected')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion(),
                    // One by one, so each review's photos are deleted with it.
                    DeleteBulkAction::make()->fetchSelectedRecords(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
