@extends('layouts.app')

@section('title', $stay->name . ' — ' . __('Where you\'ll stay') . ' — Suni Expedition')
@section('og_image', (string) $stay->image)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $stay->description), 155) ?: __(':name in :place, one of the places we book for our guests.', ['name' => $stay->name, 'place' => $stay->location]))

@section('content')

    @php
        $photos = collect([$stay->imageUrl(), ...$stay->galleryUrls()])->filter()->unique()->values();
        $paragraphs = collect(preg_split('/\R{2,}/', trim((string) $stay->description)))->filter();
    @endphp

    <section class="relative flex min-h-[72vh] items-end overflow-hidden pt-44 pb-16 sm:pt-52 sm:pb-20">
        @if ($stay->imageUrl())
            <img src="{{ $stay->imageUrl() }}" alt="{{ $stay->name }}" class="absolute inset-0 h-full w-full object-cover">
        @endif
        <div class="hero-wash-brand absolute inset-0"></div>
        <div class="relative mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('Where you\'ll stay'), 'url' => route('accommodations.index')],
                ['label' => $stay->location, 'url' => route('accommodations.index', ['location' => $stay->location_key]) . '#stays'],
                ['label' => $stay->name],
            ]" />
            <p class="mt-4 font-mono text-xs font-semibold uppercase tracking-[0.2em] text-gold">{{ collect([$stay->type, $stay->location])->filter()->join(' · ') }}</p>
            <h1 class="mt-4 max-w-3xl font-display text-4xl font-bold leading-tight text-cream sm:text-5xl">{{ $stay->name }}</h1>
            @if ($stay->level)
                <span class="mt-5 inline-flex rounded-full border border-cream/30 px-3 py-1 font-mono text-xs uppercase tracking-wide text-cream/85">{{ __($stay->level) }}</span>
            @endif
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1fr_20rem] lg:px-8">
        <div class="min-w-0">
            <h2 class="font-display text-2xl font-bold text-forest">{{ __('About this stay') }}</h2>
            <div class="mt-4 space-y-4 text-base leading-7 text-ink/85">
                @forelse ($paragraphs as $paragraph)
                    <p>{{ $paragraph }}</p>
                @empty
                    <p>{{ __(':name is one of the places we book for our guests in :place. Ask us for current photos, room types and availability.', ['name' => $stay->name, 'place' => $stay->location]) }}</p>
                @endforelse
            </div>

            @if ($photos->isNotEmpty())
                <div class="mt-12" x-data="{ open: false, index: 0, photos: @js($photos), show(i) { this.index = i; this.open = true }, step(d) { this.index = (this.index + d + this.photos.length) % this.photos.length } }"
                     @keydown.escape.window="open = false" @keydown.arrow-right.window="open && step(1)" @keydown.arrow-left.window="open && step(-1)">
                    <h2 class="font-display text-2xl font-bold text-forest">{{ __('Gallery') }}</h2>
                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($photos as $i => $photo)
                            <button type="button" @click="show({{ $i }})"
                                    class="group relative overflow-hidden rounded-xl bg-cream-2 {{ $i === 0 ? 'col-span-2 row-span-2 aspect-[4/3] sm:aspect-auto' : 'aspect-[4/3]' }}"
                                    aria-label="{{ __('Open photo :n of :total', ['n' => $i + 1, 'total' => $photos->count()]) }}">
                                <img src="{{ $photo }}" alt="{{ $stay->name }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            </button>
                        @endforeach
                    </div>

                    <template x-teleport="body">
                        <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-[120] flex items-center justify-center bg-forest-deep p-4"
                             role="dialog" aria-modal="true" aria-label="{{ __('Photo gallery') }}" @click.self="open = false"
                             @touchstart="$el.dataset.x = $event.touches[0].clientX" @touchend="const dx = $event.changedTouches[0].clientX - ($el.dataset.x || 0); if (Math.abs(dx) > 40) step(dx < 0 ? 1 : -1)">
                            <img :src="photos[index]" alt="{{ $stay->name }}" class="max-h-[85vh] max-w-full rounded-xl object-contain shadow-2xl">
                            <button type="button" @click="open = false" class="absolute right-4 top-4 flex h-11 w-11 items-center justify-center rounded-full bg-cream/10 text-cream hover:bg-cream/20" aria-label="{{ __('Close') }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" /></svg>
                            </button>
                            <template x-if="photos.length > 1">
                                <div>
                                    <button type="button" @click="step(-1)" class="absolute left-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-cream/10 text-cream hover:bg-cream/20 sm:left-6" aria-label="{{ __('Previous photo') }}">
                                        <svg class="h-5 w-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" /></svg>
                                    </button>
                                    <button type="button" @click="step(1)" class="absolute right-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-cream/10 text-cream hover:bg-cream/20 sm:right-6" aria-label="{{ __('Next photo') }}">
                                        <svg class="h-5 w-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6" /></svg>
                                    </button>
                                    <p class="absolute bottom-5 left-1/2 -translate-x-1/2 font-mono text-xs text-cream/70" x-text="`${index + 1} / ${photos.length}`"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            @endif
        </div>

        <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
            <div class="rounded-2xl border border-forest/10 bg-white p-6 shadow-sm">
                <dl class="space-y-3 text-sm">
                    @foreach (array_filter([__('Area') => $stay->location, __('Type') => $stay->type, __('Level') => $stay->level ? __($stay->level) : null]) as $term => $value)
                        <div class="flex justify-between gap-4 border-b border-forest/10 pb-3 last:border-0 last:pb-0">
                            <dt class="text-muted">{{ $term }}</dt>
                            <dd class="text-right font-medium text-forest">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
                <a href="{{ route('contact', ['stay' => $stay->name]) }}#inquiry-form" class="cta-gradient mt-6 flex w-full items-center justify-center rounded-full px-5 py-3 text-sm font-semibold text-[#241206]">
                    {{ __('Ask about this stay') }}
                </a>
                <a href="{{ route('accommodations.index', ['location' => $stay->location_key]) }}#stays" class="mt-3 block text-center text-sm font-semibold text-forest hover:text-orange">
                    {{ __('More stays in :place', ['place' => $stay->location]) }} &rarr;
                </a>
            </div>
        </aside>
    </section>

    @if ($tours->isNotEmpty())
        <section class="border-t border-forest/10 bg-cream-2 py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <h2 class="font-display text-2xl font-bold text-forest">{{ __('Packages that stay here') }}</h2>
                <p class="mt-2 text-muted">{{ trans_choice(':count trip includes a night at :name.|:count trips include a night at :name.', $tours->count(), ['name' => $stay->name]) }}</p>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($tours as $tour)
                        <x-tour-card :tour="$tour" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($nearby->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="font-display text-2xl font-bold text-forest">{{ __('Other stays in :place', ['place' => $stay->location]) }}</h2>
            <div class="mt-8 grid gap-6 sm:grid-cols-3">
                @foreach ($nearby as $other)
                    <a href="{{ route('accommodations.show', $other) }}" class="group overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="aspect-[4/3] overflow-hidden bg-cream-2">
                            @if ($other->imageUrl())
                                <img src="{{ $other->imageUrl() }}" alt="{{ $other->name }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-display text-lg font-bold text-forest group-hover:text-orange">{{ $other->name }}</h3>
                            <p class="mt-1 text-sm text-muted">{{ collect([$other->type, $other->level])->filter()->join(' · ') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

@endsection
