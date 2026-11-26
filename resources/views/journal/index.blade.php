@extends('layouts.app')

@section('title', 'Journal — Suni Expedition')

@section('content')

    <section class="relative flex min-h-[100svh] items-center overflow-hidden pt-44 pb-32 sm:pt-52 sm:pb-40">
        <img src="{{ asset('images/blog/great-migration.jpg') }}" alt="{{ __('The Journal') }}" class="absolute inset-0 h-full w-full object-cover">
        <div class="hero-wash-brand absolute inset-0"></div>
        <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('Journal')],
            ]" />
            <p class="mt-4 font-mono text-xs font-semibold uppercase tracking-[0.2em] text-gold">{{ __('Field notes') }}</p>
            <h1 class="mt-5 max-w-2xl font-display text-4xl font-bold leading-tight text-cream sm:text-5xl">{{ __('The Journal') }}</h1>
            <p class="mt-6 max-w-xl text-lg leading-7 text-cream/75">{{ __('Route comparisons, packing lists, and honest field notes from our guides.') }}</p>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('contact') }}" class="cta-gradient rounded-full px-6 py-3 text-sm font-semibold text-[#241206] shadow-[0_10px_30px_rgba(217,96,15,0.35)] transition hover:-translate-y-0.5">
                    {{ __('Plan my trip') }}
                </a>
                <a href="#journal-grid" class="rounded-full border border-cream/25 px-6 py-3 text-sm font-semibold text-cream hover:bg-cream/10">
                    {{ __('Browse articles below') }}
                </a>
            </div>
        </div>
    </section>

    <section id="journal-grid" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <a href="{{ route('journal.show', $post) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    @if ($post->cover_image)
                        <div class="h-44 overflow-hidden">
                            <img src="{{ asset($post->cover_image) }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        </div>
                    @endif
                    <div class="flex flex-1 flex-col p-6">
                        <p class="font-mono text-xs font-semibold uppercase tracking-wide text-orange">{{ $post->published_at->format('M j, Y') }}</p>
                        <h2 class="mt-2 font-display text-lg font-bold text-forest group-hover:text-orange">{{ $post->title }}</h2>
                        <p class="mt-2 flex-1 text-sm leading-6 text-muted">{{ $post->excerpt }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-orange">{{ __('Read more') }} <span class="rtl:rotate-180">&rarr;</span></span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </section>

@endsection
