<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Mail\QuoteMail;
use App\Models\Quote;
use App\Models\Tour;
use App\Models\User;
use App\Support\QuotePdf;
use Filament\Actions\Action;
use Filament\Notifications\Notification as AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Throwable;

class InstantQuoteController extends Controller
{
    public function store(Request $request, Tour $tour)
    {
        abort_unless($tour->price_from > 0, 404);
        $backToDialog = route('tours.show', $tour) . '#instant-quote';

        // Honeypot: real visitors never see this field, bots fill everything. Pretend it worked, store nothing.
        if (filled($request->input('website'))) {
            return redirect()->to($backToDialog)->with('instant_quote', ['reference' => null, 'download_url' => null]);
        }

        $data = $request->validateWithBag('quote', [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'adults' => ['required', 'integer', 'between:1,50'],
            'children' => ['nullable', 'integer', 'between:0,50'],
        ], [
            'travel_date.after_or_equal' => __('Please choose a travel date that is today or later.'),
        ]);

        $start = Carbon::parse($data['travel_date']);
        $quote = Quote::create([
            'source' => 'website',
            'status' => 'draft',
            'tour_id' => $tour->id,
            'customer_name' => $data['name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'] ?? null,
            'customer_country' => $data['country'] ?? null,
            'adults' => $data['adults'],
            'children' => $data['children'] ?? 0,
            'travel_start' => $start->toDateString(),
            'travel_end' => $start->copy()->addDays($tour->duration_days - 1)->toDateString(),
            'items' => Quote::packageItems($tour, (int) $data['adults'], (int) ($data['children'] ?? 0)),
            'notes' => config('quotes.instant_note'),
            'show_bank_details' => true,
            'valid_until' => now()->addDays(config('quotes.validity_days'))->toDateString(),
        ]);

        $this->notifyOffice($quote);

        return redirect()->to($backToDialog)->with('instant_quote', [
            'reference' => $quote->reference,
            'download_url' => URL::temporarySignedRoute('quotes.download', now()->addDays(7), ['quote' => $quote]),
        ]);
    }

    public function download(Quote $quote)
    {
        return response()->streamDownload(fn () => print(QuotePdf::output($quote)), $quote->pdfFilename(), ['Content-Type' => 'application/pdf']);
    }

    // A failed email or notification must never cost the visitor their quote.
    private function notifyOffice(Quote $quote): void
    {
        try {
            Mail::to(config('quotes.office_email'))->send(new QuoteMail($quote, forOffice: true));
        } catch (Throwable $e) {
            report($e);
        }

        try {
            $notification = AdminNotification::make()
                ->title("New instant quote {$quote->reference}")
                ->body("{$quote->customer_name} · {$quote->tour->title} · " . Quote::money($quote->total))
                ->icon('heroicon-o-document-currency-dollar')
                ->actions([
                    Action::make('open')->label('Open quote')->url(QuoteResource::getUrl('edit', ['record' => $quote], panel: 'admin')),
                ]);

            // Filament queues database notifications; shared hosting often has no queue worker, so send now.
            Notification::sendNow(User::all(), $notification->toDatabase());
        } catch (Throwable $e) {
            report($e);
        }
    }
}
