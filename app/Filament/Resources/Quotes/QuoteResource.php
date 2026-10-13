<?php

namespace App\Filament\Resources\Quotes;

use App\Filament\Resources\Quotes\Pages\CreateQuote;
use App\Filament\Resources\Quotes\Pages\EditQuote;
use App\Filament\Resources\Quotes\Pages\ListQuotes;
use App\Filament\Resources\Quotes\Schemas\QuoteForm;
use App\Filament\Resources\Quotes\Tables\QuotesTable;
use App\Mail\QuoteMail;
use App\Models\Quote;
use App\Support\QuotePdf;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Throwable;
use UnitEnum;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    protected static string | UnitEnum | null $navigationGroup = 'Bookings';

    protected static ?string $navigationLabel = 'Quotes';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'customer_name', 'customer_email'];
    }

    public static function statusColor(?string $status): string
    {
        return match ($status) {
            'sent' => 'info',
            'accepted' => 'success',
            'declined' => 'danger',
            default => 'gray',
        };
    }

    public static function downloadAction(): Action
    {
        return Action::make('pdf')
            ->label('Download PDF')
            ->icon(Heroicon::OutlinedDocumentArrowDown)
            ->color('gray')
            ->action(fn (Quote $record) => response()->streamDownload(fn () => print(QuotePdf::output($record)), $record->pdfFilename(), ['Content-Type' => 'application/pdf']));
    }

    public static function emailAction(): Action
    {
        return Action::make('email')
            ->label('Email to customer')
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->requiresConfirmation()
            ->modalHeading('Email this quote?')
            ->modalDescription(fn (Quote $record): string => "The PDF for {$record->reference} will be sent to {$record->customer_email}, and the quote will be marked as sent.")
            ->modalSubmitActionLabel('Send email')
            ->action(function (Quote $record): void {
                try {
                    Mail::to($record->customer_email, $record->customer_name)->send(new QuoteMail($record));
                } catch (Throwable $e) {
                    report($e);
                    Notification::make()->title('The email could not be sent')->body($e->getMessage())->danger()->persistent()->send();

                    return;
                }

                $record->update(['status' => 'sent']);
                Notification::make()->title("Quote {$record->reference} emailed to {$record->customer_email}")->success()->send();
            });
    }

    public static function form(Schema $schema): Schema
    {
        return QuoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotes::route('/'),
            'create' => CreateQuote::route('/create'),
            'edit' => EditQuote::route('/{record}/edit'),
        ];
    }
}
