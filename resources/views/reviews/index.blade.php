@extends('layouts.app')

@section('title', 'Traveler Reviews — Suni Expedition')
@section('meta_description', 'Honest reviews from travelers who went on safari, climbed Kilimanjaro or relaxed in Zanzibar with Suni Expedition.')

@section('content')

    <section class="relative flex min-h-[86vh] items-center overflow-hidden pt-44 pb-28 sm:pt-52 sm:pb-32">
        <img src="{{ asset('images/tours/southern-circuit.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        <div class="hero-wash-brand absolute inset-0"></div>
        <div class="relative mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('Reviews')],
            ]" />
            <p class="mt-4 font-mono text-xs font-semibold uppercase tracking-[0.2em] text-gold">{{ __('Traveler reviews') }}</p>
            <h1 class="mt-5 max-w-2xl font-display text-4xl font-bold leading-tight text-cream sm:text-5xl">{!! __('What our :em say', ['em' => '<em>' . __('travelers') . '</em>']) !!}</h1>

            @if ($reviewCount)
                <div class="mt-6 flex flex-wrap items-center gap-3 text-cream">
                    <span class="font-display text-4xl font-bold">{{ number_format($averageRating, 1) }}</span>
                    <div>
                        <div class="flex gap-0.5 text-gold" aria-hidden="true">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 fill-current {{ $i <= round($averageRating) ? '' : 'opacity-30' }}" viewBox="0 0 20 20"><path d="M10 1.5l2.6 5.6 6.1.6-4.6 4.1 1.3 6-5.4-3-5.4 3 1.3-6-4.6-4.1 6.1-.6z" /></svg>
                            @endfor
                        </div>
                        <p class="mt-0.5 text-sm text-cream/75">{{ trans_choice('Average from :count review|Average from :count reviews', $reviewCount, ['count' => $reviewCount]) }}</p>
                    </div>
                </div>
            @endif

            <div class="mt-10 flex flex-wrap gap-4" x-data="modal({{ $errors->review->any() ? 'true' : 'false' }})">
                <button type="button" @click="open = true" class="cta-gradient inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-semibold text-[#241206] shadow-[0_10px_30px_rgba(217,96,15,0.35)] transition hover:-translate-y-0.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                    {{ __('Write a review') }}
                </button>
                <a href="#reviews-grid" class="rounded-full border border-cream/25 px-6 py-3 text-sm font-semibold text-cream hover:bg-cream/10">
                    {{ __('Read the reviews') }}
                </a>
                <x-modal>
                    <x-review-form :tours="$tours" />
                </x-modal>
            </div>
        </div>
    </section>

    <section id="reviews-grid" class="mx-auto max-w-6xl scroll-mt-24 px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-display text-2xl font-bold text-forest">{{ __('All reviews') }}</h2>
            <x-filter-chips :links="$filterLinks" :label="__('Filter reviews by trip type')" fragment="reviews-grid" />
        </div>

        @if ($reviews->isEmpty())
            <p class="mt-10 text-muted">{{ __('No reviews for this trip type yet — be the first to share yours.') }}</p>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($reviews as $testimonial)
                    <x-testimonial-card :testimonial="$testimonial" />
                @endforeach
            </div>

            <div class="mt-12">
                {{ $reviews->fragment('reviews-grid')->links() }}
            </div>
        @endif

        <x-review-badges class="mt-16 border-t border-forest/10 pt-12" />
    </section>

@endsection
