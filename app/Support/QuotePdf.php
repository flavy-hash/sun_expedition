<?php

namespace App\Support;

use App\Models\Quote;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotePdf
{
    public static function output(Quote $quote): string
    {
        $quote->loadMissing('tour');

        return Pdf::loadView('pdf.quote', [
            'quote' => $quote,
            'company' => config('quotes.company'),
            'terms' => config('quotes.terms'),
            // Embedded so dompdf never has to fetch anything over the network.
            'logo' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo-pdf.png'))),
            'bankAccounts' => $quote->show_bank_details ? Setting::bankAccounts() : [],
            'paymentInstructions' => $quote->show_bank_details ? Setting::get('payment_instructions') : null,
        ])
            ->setPaper('a4')
            // Embed only the glyphs actually used: without this a one-page quote is ~880 KB, with it ~30 KB.
            ->setOption(['isFontSubsettingEnabled' => true, 'defaultFont' => 'DejaVu Sans', 'isRemoteEnabled' => false])
            ->output();
    }
}
