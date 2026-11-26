@extends('layouts.app')

@section('title', ($location ? __('Stays in :place', ['place' => $location]) : __('Where you\'ll stay')) . ' — Suni Expedition')
@section('meta_description', __('The lodges, tented camps and beach hotels we use on our safaris, climbs and Zanzibar trips.'))

@section('content')

    <section class="relative flex min-h-[86vh] items-center overflow-hidden pt-44 pb-28 sm:pt-52 sm:pb-32">
        <img src="{{ asset('images/stays/serengeti-ridge-lodge.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        <div class="hero-wash-brand absolute inset-0"></div>
        <div class="relative mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="array_values(array_filter([
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('Where you\'ll stay'), 'url' => $location ? route('accommodations.index') : null],
                $location ? ['label' => $location] : null,
            ]))" />
            <p class="mt-4 font-mono text-xs font-semibold uppercase tracking-[0.2em] text-gold">{{ __('Lodges · camps · hotels') }}</p>
            <h1 class="mt-5 max-w-2xl font-display text-4xl font-bold leading-tight text-cream sm:text-5xl">
                @if ($location)
                    {!! __('Stays in :em', ['em' => '<em>' . e($location) . '</em>']) !!}
                @else
                    {!! __('Where you\'ll :em', ['em' => '<em>' . __('stay') . '</em>']) !!}
                @endif
            </h1>
            <p class="mt-6 max-w-xl text-lg leading-7 text-cream/75">{{ __('Every lodge, tented camp and hotel we book, chosen by our guides for location first: close to the wildlife, the trailhead or the beach.') }}</p>
        </div>
    </section>

    <section id="stays" class="mx-auto max-w-6xl scroll-mt-24 px-4 py-16 sm:px-6 lg:px-8">
        <x-filter-chips :links="$filterLinks" :label="__('Filter stays by area')" fragment="stays" />

        @if ($stays->isEmpty())
            <div class="mt-10 rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-12 text-center">
                <p class="font-display text-lg font-bold text-forest">{{ __('No stays listed here yet') }}</p>
                <p class="mt-2 text-sm text-muted">{{ __('Ask us and we\'ll suggest somewhere that fits your trip.') }}</p>
                <a href="{{ route('accommodations.index') }}" class="mt-5 inline-block text-sm font-semibold text-orange hover:text-forest">{{ __('See all stays') }} &rarr;</a>
            </div>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($stays as $stay)
                    <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
                        <div class="relative aspect-[4/3] bg-cream-2">
                            @if ($stay->imageUrl())
                                <a href="{{ route('accommodations.show', $stay) }}" tabindex="-1" aria-hidden="true"><img src="{{ $stay->imageUrl() }}" alt="" loading="lazy" class="h-full w-full object-cover"></a>
                            @endif
                            <a href="{{ route('accommodations.index', ['location' => $stay->location_key]) }}#stays"
                               class="absolute left-3 top-3 rounded-full bg-forest-deep/85 px-3 py-1 text-xs font-semibold text-cream hover:bg-forest-deep">{{ $stay->location }}</a>
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <h2 class="font-display text-lg font-bold text-forest"><a href="{{ route('accommodations.show', $stay) }}" class="hover:text-orange">{{ $stay->name }}</a></h2>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach (array_filter([$stay->type, $stay->level]) as $chip)
                                    <span class="rounded-full bg-cream-2 px-2.5 py-0.5 text-xs font-medium text-forest">{{ $chip }}</span>
                                @endforeach
                            </div>
                            @if (filled($stay->description))
                                <p class="mt-3 flex-1 text-sm leading-6 text-muted">{{ \Illuminate\Support\Str::limit($stay->description, 160) }}</p>
                            @endif
                            <a href="{{ route('accommodations.show', $stay) }}"
                               class="mt-5 inline-flex w-fit items-center gap-1 text-sm font-semibold text-orange hover:text-forest">
                                {{ __('View stay') }} <span class="rtl:rotate-180">&rarr;</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

@endsection
