@extends('layouts.app')

@section('title', 'Privacy Policy — Suni Expedition')

@section('content')

    <section class="hero-wash-brand pt-48 pb-32 sm:pt-60 sm:pb-40">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('Privacy policy')],
            ]" />
            <h1 class="mt-4 font-display text-4xl font-bold text-cream">
                {{ $page->title }}
                @if ($page->title_highlight)
                    <em>{{ $page->title_highlight }}</em>
                @endif
            </h1>
            @if ($page->intro)
                <p class="mt-4 max-w-2xl text-cream/75">{{ $page->intro }}</p>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="space-y-5 text-sm leading-7 text-muted">
            @foreach ($page->paragraphs() as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
        <p class="mt-10 text-xs text-muted/80">{{ __('Last updated :date', ['date' => $page->updated_at->format('j F Y')]) }}</p>
    </section>

@endsection
