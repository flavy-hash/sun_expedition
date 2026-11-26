@extends('layouts.app')

@section('title', $post->title . ' — Suni Expedition')
@section('meta_description', $post->excerpt)
@section('og_image', (string) $post->cover_image)
@section('og_type', 'article')

@section('content')

    <section class="relative flex min-h-[80vh] items-center overflow-hidden pt-44 pb-24 sm:pt-52 sm:pb-28">
        @if ($post->cover_image)
            <img src="{{ asset($post->cover_image) }}" alt="{{ $post->title }}" class="absolute inset-0 h-full w-full object-cover">
        @endif
        <div class="hero-wash-brand absolute inset-0"></div>
        <div class="relative mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('Journal'), 'url' => route('journal.index')],
                ['label' => $post->title],
            ]" />
            <p class="mt-4 font-mono text-xs font-semibold uppercase tracking-wide text-gold">{{ $post->published_at->format('F j, Y') }} &middot; {{ $post->author_name }}</p>
            <h1 class="mt-2 font-display text-4xl font-bold leading-tight text-cream sm:text-5xl">{{ $post->title }}</h1>
            <p class="mt-6 max-w-xl text-lg leading-7 text-cream/75">{{ $post->excerpt }}</p>
        </div>
    </section>

    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="space-y-5 text-sm leading-7 text-muted">
            @foreach (explode("\n\n", $post->body) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>

        <div class="mt-12">
            <x-inquiry-form :title="__('Ready to start planning?')" :description="__('Whatever inspired this post, we can help turn it into an itinerary.')" />
        </div>
    </article>

    @if ($recentPosts->isNotEmpty())
        <section class="border-t border-forest/10 bg-cream-2 py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <h2 class="font-display text-2xl font-bold text-forest">{{ __('More from the journal') }}</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-3">
                    @foreach ($recentPosts as $recent)
                        <a href="{{ route('journal.show', $recent) }}" class="group rounded-2xl border border-forest/10 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <h3 class="font-display text-lg font-bold text-forest group-hover:text-orange">{{ $recent->title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-muted">{{ $recent->excerpt }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
