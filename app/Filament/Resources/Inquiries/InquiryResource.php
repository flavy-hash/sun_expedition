<?php

namespace App\Filament\Resources\Inquiries;

use App\Filament\Resources\Inquiries\Pages\ListInquiries;
use App\Filament\Resources\Inquiries\Pages\ViewInquiry;
use App\Models\Inquiry;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class InquiryResource extends Resource
{
    public const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'];

    protected static ?string $model = Inquiry::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string | UnitEnum | null $navigationGroup = 'Guests';

    protected static ?string $navigationLabel = 'Inquiries';

    protected static ?string $modelLabel = 'inquiry';

    protected static ?string $pluralModelLabel = 'inquiries';

    protected static ?string $slug = 'inquiries';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $new = Inquiry::where('status', 'new')->count();

        return $new ? (string) $new : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New, not yet answered';
    }

    public static function statusColor(?string $status): string
    {
        return match ($status) {
            'new' => 'primary',
            'contacted' => 'info',
            default => 'gray',
        };
    }

    // Shared by the table rows and the detail page header.
    public static function statusActions(): array
    {
        return collect(['contacted' => Heroicon::OutlinedCheckCircle, 'closed' => Heroicon::OutlinedEyeSlash, 'new' => Heroicon::OutlinedInboxArrowDown])
            ->map(fn (Heroicon $icon, string $status) => Action::make('mark_' . $status)
                ->label($status === 'new' ? 'Reopen as new' : 'Mark ' . strtolower(self::STATUSES[$status]))
                ->icon($icon)
                ->color(self::statusColor($status))
                ->visible(fn (Inquiry $record): bool => $record->status !== $status)
                ->action(function (Inquiry $record) use ($status): void {
                    $record->update(['status' => $status]);
                    Notification::make()->title('Marked ' . strtolower(self::STATUSES[$status]))->success()->send();
                }))
            ->values()
            ->all();
    }

    public static function replyActions(): array
    {
        return [
            Action::make('replyEmail')
                ->label('Reply by email')
                ->icon(Heroicon::OutlinedEnvelope)
                ->url(fn (Inquiry $record): string => 'mailto:' . $record->email . '?subject=' . rawurlencode('Your Suni Expedition trip request')),
            Action::make('replyWhatsApp')
                ->label('WhatsApp')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->visible(fn (Inquiry $record): bool => strlen(preg_replace('/\D+/', '', (string) $record->phone)) >= 7)
                ->url(fn (Inquiry $record): string => 'https://wa.me/' . preg_replace('/\D+/', '', $record->phone) . '?text=' . rawurlencode("Hi {$record->name}, thanks for your trip request with Suni Expedition!"))
                ->openUrlInNewTab(),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Trip request')
                ->columnSpanFull()
                ->columns(3)
                ->schema([
                    TextEntry::make('name')->weight('medium'),
                    TextEntry::make('email')->copyable(),
                    TextEntry::make('phone')->label('Phone / WhatsApp')->placeholder('—')->copyable(),
                    TextEntry::make('tour.title')->label('Package')->placeholder('Not sure yet — help me choose'),
                    TextEntry::make('preferred_dates')->label('Preferred dates')->placeholder('—'),
                    TextEntry::make('group_size')->label('Group size')->placeholder('—'),
                    TextEntry::make('status')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => self::STATUSES[$state] ?? $state)
                        ->color(fn (string $state): string => self::statusColor($state)),
                    TextEntry::make('created_at')->label('Received')->dateTime('M j, Y · H:i'),
                    TextEntry::make('message')->placeholder('No message')->columnSpanFull()->prose(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Inquiry $record): string => self::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
                TextColumn::make('name')
                    ->searchable(['name', 'email', 'phone'])
                    ->weight('medium')
                    ->description(fn (Inquiry $record): string => $record->email),
                TextColumn::make('phone')->label('Phone / WhatsApp')->placeholder('—'),
                TextColumn::make('tour.title')->label('Package')->placeholder('Not sure yet')->wrap(),
                TextColumn::make('preferred_dates')->label('Dates')->placeholder('—')->wrap(),
                TextColumn::make('group_size')->label('Group')->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => self::statusColor($state)),
            ])
            ->filters([
                SelectFilter::make('tour')->label('Package')->relationship('tour', 'title'),
            ])
            ->recordActions([
                ViewAction::make(),
                ActionGroup::make([
                    ...self::replyActions(),
                    ...self::statusActions(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markContacted')
                        ->label('Mark contacted')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'contacted']))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('markClosed')
                        ->label('Mark closed')
                        ->icon(Heroicon::OutlinedEyeSlash)
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'closed']))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
            'view' => ViewInquiry::route('/{record}'),
        ];
    }
}
