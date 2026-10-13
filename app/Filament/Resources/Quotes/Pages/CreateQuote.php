<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Filament\Resources\Quotes\Schemas\QuoteForm;
use App\Models\Inquiry;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Throwable;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    // Set by the "Create quote" action on an inquiry: /admin/quotes/create?inquiry=12
    #[Url]
    public ?int $inquiry = null;

    protected function fillForm(): void
    {
        parent::fillForm(); // defaults (status, valid until, bank details…)

        $inquiry = $this->inquiry ? Inquiry::with('tour')->find($this->inquiry) : null;
        if (! $inquiry) {
            return;
        }

        $adults = max(1, (int) ($inquiry->group_size ?: 2));
        $start = $this->parseDate($inquiry->preferred_dates);

        $this->form->fill(array_merge($this->form->getRawState(), array_filter([
            'customer_name' => $inquiry->name,
            'customer_email' => $inquiry->email,
            'customer_phone' => $inquiry->phone,
            'tour_id' => $inquiry->tour_id,
            'adults' => $adults,
            'children' => 0,
            'travel_start' => $start?->toDateString(),
            'travel_end' => $start && $inquiry->tour ? $start->copy()->addDays($inquiry->tour->duration_days - 1)->toDateString() : null,
            'items' => $inquiry->tour ? QuoteForm::packageItemsState($inquiry->tour, $adults, 0) : null,
            // Preferred dates are free text on the website form, so they're kept for the admin to read.
            'notes' => filled($inquiry->preferred_dates) ? "Preferred dates: {$inquiry->preferred_dates}" : null,
        ], fn ($value) => $value !== null)));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['source'] = 'admin';
        $data['inquiry_id'] = $this->inquiry && Inquiry::whereKey($this->inquiry)->exists() ? $this->inquiry : null;

        return $data;
    }

    /** Only exact dates (2026-07-14 or 14/07/2026); phrases like "mid-July, flexible" stay in the notes. */
    private function parseDate(?string $text): ?Carbon
    {
        foreach (['Y-m-d', 'd/m/Y'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, trim((string) $text));
                if ($date && $date->format($format) === trim((string) $text)) {
                    return $date->startOfDay();
                }
            } catch (Throwable) {
                // Not this format.
            }
        }

        return null;
    }
}
