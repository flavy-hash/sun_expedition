<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Quotes\Pages\CreateQuote;
use App\Filament\Resources\Quotes\Pages\ListQuotes;
use App\Mail\QuoteMail;
use App\Models\Inquiry;
use App\Models\Quote;
use App\Models\Setting;
use App\Models\Tour;
use App\Models\User;
use App\Support\QuotePdf;
use Database\Seeders\TourSeeder;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    private Tour $tour;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TourSeeder::class);
        $this->tour = Tour::where('slug', 'zanzibar-beach-escape')->firstOrFail(); // $890, 4 days
    }

    private function quote(array $attributes = []): Quote
    {
        return Quote::create(array_merge([
            'customer_name' => 'Ana Silva',
            'customer_email' => 'ana@example.com',
            'tour_id' => $this->tour->id,
            'adults' => 2,
            'items' => [['description' => 'Zanzibar Beach Escape — adult', 'quantity' => 2, 'unit_price' => 890]],
            'valid_until' => now()->addDays(14)->toDateString(),
        ], $attributes));
    }

    private function setBankAccounts(): void
    {
        Setting::put('bank_accounts', [
            ['bank_name' => 'CRDB Bank', 'account_name' => 'Suni Expedition Ltd', 'account_number' => '0150-123456', 'branch' => 'Arusha', 'swift' => 'CORUTZTZ', 'currency' => 'USD'],
            ['bank_name' => 'NMB Bank', 'account_name' => 'Suni Expedition Ltd', 'account_number' => '2091-777', 'currency' => 'TZS'],
            ['bank_name' => 'Half-filled Bank', 'account_name' => '', 'account_number' => ''],
        ]);
        Setting::put('payment_instructions', 'Bank charges are paid by the sender.');
    }

    private function pdfHtml(Quote $quote): string
    {
        // dompdf subsets and encodes fonts, so content is checked on the HTML it renders from.
        return view('pdf.quote', [
            'quote' => $quote->load('tour'),
            'company' => config('quotes.company'),
            'terms' => config('quotes.terms'),
            'logo' => '',
            'bankAccounts' => $quote->show_bank_details ? Setting::bankAccounts() : [],
            'paymentInstructions' => $quote->show_bank_details ? Setting::get('payment_instructions') : null,
        ])->render();
    }

    public function test_reference_and_totals(): void
    {
        $quote = $this->quote(['discount' => 80, 'items' => [
            ['description' => 'Adult', 'quantity' => 2, 'unit_price' => 890],
            ['description' => 'Child', 'quantity' => 1, 'unit_price' => '623.00'],
        ]]);

        $this->assertSame(sprintf('QT-%s-%06d', now()->format('Y'), $quote->id), $quote->fresh()->reference);
        $this->assertSame(2403.0, $quote->subtotal);
        $this->assertSame(2323.0, $quote->total);
        $this->assertSame('$2,323.00', Quote::money($quote->total));
        $this->assertSame("Suni-Expedition-Quote-{$quote->fresh()->reference}.pdf", $quote->fresh()->pdfFilename());

        $quote->update(['discount' => 5000]);
        $this->assertSame(0.0, $quote->fresh()->total, 'A discount can never make the total negative.');
    }

    public function test_package_items_price_children_at_the_child_rate(): void
    {
        config(['quotes.child_rate' => 0.5]);

        $this->assertSame([
            ['description' => 'Zanzibar Beach Escape — adult', 'quantity' => 2, 'unit_price' => 890.0],
            ['description' => 'Zanzibar Beach Escape — child', 'quantity' => 1, 'unit_price' => 445.0],
        ], Quote::packageItems($this->tour, 2, 1));
    }

    public function test_pdf_contains_the_quote_and_only_complete_bank_accounts(): void
    {
        $this->setBankAccounts();
        $quote = $this->quote(['notes' => 'Includes a sunset dhow cruise.'])->fresh();

        $html = $this->pdfHtml($quote);
        foreach (['QUOTATION', $quote->reference, 'Ana Silva', 'Zanzibar Beach Escape', '$1,780.00', 'Includes a sunset dhow cruise.',
            'CRDB Bank', 'CORUTZTZ', 'NMB Bank', "Payment reference: {$quote->reference}", 'Bank charges are paid by the sender.'] as $text) {
            $this->assertStringContainsString(e($text), $html);
        }
        $this->assertStringNotContainsString('Half-filled Bank', $html);

        $pdf = QuotePdf::output($quote);
        $this->assertStringStartsWith('%PDF', $pdf);
        $this->assertLessThan(150_000, strlen($pdf), 'Font subsetting keeps the PDF small.');
    }

    public function test_bank_details_can_be_hidden_per_quote(): void
    {
        $this->setBankAccounts();
        $html = $this->pdfHtml($this->quote(['show_bank_details' => false])->fresh());

        $this->assertStringNotContainsString('CRDB Bank', $html);
        $this->assertStringNotContainsString('Payment details', $html);
    }

    public function test_site_settings_save_bank_accounts(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(SiteSettings::getUrl())->assertOk();

        Livewire::test(SiteSettings::class)
            ->fillForm([
                'bank_accounts' => [['bank_name' => 'Stanbic', 'account_name' => 'Suni Expedition', 'account_number' => '9100', 'currency' => 'USD']],
                'payment_instructions' => 'Quote your reference.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Stanbic', Setting::bankAccounts()[0]['bank_name']);
        $this->assertSame('Quote your reference.', Setting::get('payment_instructions'));
    }

    public function test_admin_creates_a_quote_from_an_inquiry(): void
    {
        $this->actingAs(User::factory()->create());
        $inquiry = Inquiry::create(['name' => 'Ben Okafor', 'email' => 'ben@example.com', 'phone' => '+234 800', 'tour_id' => $this->tour->id, 'group_size' => 3, 'preferred_dates' => '2027-02-10']);

        $page = Livewire::withQueryParams(['inquiry' => $inquiry->id])->test(CreateQuote::class)
            ->assertSchemaStateSet([
                'customer_name' => 'Ben Okafor',
                'customer_email' => 'ben@example.com',
                'tour_id' => $this->tour->id,
                'adults' => 3,
                'travel_start' => '2027-02-10',
                'travel_end' => '2027-02-13',
            ]);
        $this->assertEquals([['description' => 'Zanzibar Beach Escape — adult', 'quantity' => 3, 'unit_price' => 890]], array_values($page->get('data.items')));

        $page->call('create')->assertHasNoFormErrors();

        $quote = Quote::sole();
        $this->assertSame([$inquiry->id, 'admin', 2670.0], [$quote->inquiry_id, $quote->source, $quote->total]);
    }

    public function test_free_text_dates_stay_in_the_notes(): void
    {
        $this->actingAs(User::factory()->create());
        $inquiry = Inquiry::create(['name' => 'Cleo', 'email' => 'cleo@example.com', 'preferred_dates' => 'mid-July, flexible']);

        Livewire::withQueryParams(['inquiry' => $inquiry->id])->test(CreateQuote::class)
            ->assertSchemaStateSet(['travel_start' => null, 'notes' => 'Preferred dates: mid-July, flexible']);
    }

    public function test_download_and_email_actions(): void
    {
        $this->actingAs(User::factory()->create());
        $quote = $this->quote()->fresh();
        Mail::fake();

        Livewire::test(ListQuotes::class)
            ->callTableAction('pdf', $quote)
            ->assertFileDownloaded($quote->pdfFilename());

        Livewire::test(ListQuotes::class)->callTableAction('email', $quote);

        Mail::assertSent(QuoteMail::class, fn (QuoteMail $mail) => $mail->hasTo('ana@example.com')
            && ! $mail->forOffice
            && collect($mail->attachments())->contains(fn ($attachment) => $attachment->as === $quote->pdfFilename()));
        $this->assertSame('sent', $quote->fresh()->status);
    }

    public function test_a_failed_email_keeps_the_status_and_warns_the_admin(): void
    {
        $this->actingAs(User::factory()->create());
        $quote = $this->quote()->fresh();
        Mail::shouldReceive('to')->andThrow(new Exception('SMTP server unreachable'));

        Livewire::test(ListQuotes::class)
            ->callTableAction('email', $quote)
            ->assertNotified('The email could not be sent');

        $this->assertSame('draft', $quote->fresh()->status);
    }

    private function requestInstantQuote(array $overrides = [])
    {
        return $this->post(route('tours.quote', $this->tour), array_merge([
            'name' => 'Dana Lee',
            'email' => 'dana@example.com',
            'phone' => '+1 555 0100',
            'country' => 'Canada',
            'travel_date' => now()->addMonths(2)->toDateString(),
            'adults' => 2,
            'children' => 1,
        ], $overrides));
    }

    public function test_visitor_gets_an_instant_quote_with_a_signed_download_link(): void
    {
        Mail::fake();
        $admin = User::factory()->create();

        $response = $this->requestInstantQuote();
        $response->assertRedirect(route('tours.show', $this->tour) . '#instant-quote');

        $quote = Quote::sole();
        $this->assertSame(['website', 'draft', 2, 1], [$quote->source, $quote->status, $quote->adults, $quote->children]);
        $this->assertSame(890 * 2 + round(890 * config('quotes.child_rate'), 2), $quote->total);
        $this->assertSame(config('quotes.instant_note'), $quote->notes);
        $this->assertSame($quote->travel_start->copy()->addDays(3)->toDateString(), $quote->travel_end->toDateString());

        Mail::assertSent(QuoteMail::class, fn (QuoteMail $mail) => $mail->hasTo(config('quotes.office_email')) && $mail->forOffice);
        $this->assertSame(1, $admin->notifications()->count(), 'Admins are notified straight away, without a queue worker.');

        $link = session('instant_quote')['download_url'];
        $this->get(route('tours.show', $this->tour))->assertSee('Download Quote (PDF)')->assertSee('data-open-on-load', false);
        $this->get($link)->assertOk()->assertDownload($quote->pdfFilename());

        $this->get(route('quotes.download', $quote))->assertForbidden();          // unsigned
        $this->get($link . 'tampered')->assertForbidden();                        // tampered
        $this->travel(8)->days();
        $this->get($link)->assertForbidden();                                     // expired after 7 days
    }

    public function test_instant_quote_validation_uses_its_own_error_bag(): void
    {
        $this->requestInstantQuote(['name' => '', 'travel_date' => now()->subDay()->toDateString()])
            ->assertSessionHasErrors(['name', 'travel_date'], null, 'quote');

        $this->assertDatabaseCount('quotes', 0);
    }

    public function test_honeypot_submissions_are_not_stored(): void
    {
        Mail::fake();
        $this->requestInstantQuote(['website' => 'http://spam.example'])->assertRedirect();

        $this->assertDatabaseCount('quotes', 0);
        Mail::assertNothingSent();
    }

    public function test_packages_without_a_price_show_on_request_and_no_instant_quote(): void
    {
        $this->tour->update(['price_from' => 0]);

        $this->get(route('tours.show', $this->tour))
            ->assertSee('On request')
            ->assertDontSee('Get an Instant Quote');
        $this->requestInstantQuote()->assertNotFound();
    }
}
