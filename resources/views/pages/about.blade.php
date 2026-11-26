@extends('layouts.app')

@section('title', 'About — Suni Expedition')

@section('content')

    <section class="hero-wash-brand pt-48 pb-32 sm:pt-60 sm:pb-40">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('About')],
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

    <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="space-y-5 text-sm leading-7 text-muted">
            @foreach ($page->paragraphs() as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>

        @if ($page->stats)
            @php $statCols = [1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3', 4 => 'sm:grid-cols-4'][min(count($page->stats), 4)]; @endphp
            <div class="mt-12 grid gap-6 {{ $statCols }}">
                @foreach ($page->stats as $stat)
                    <div class="rounded-2xl border border-forest/10 bg-white p-6 text-center shadow-sm">
                        <p class="font-display text-3xl font-bold text-orange">{{ $stat['value'] }}</p>
                        <p class="mt-2 text-sm text-muted">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="border-t border-forest/10 bg-cream-2 py-16">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <x-inquiry-form />
        </div>
    </section>

@endsection
