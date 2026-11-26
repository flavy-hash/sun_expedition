@extends('layouts.app')

@section('title', 'FAQ — Suni Expedition')

@section('content')

    <section class="hero-wash-brand pt-48 pb-32 sm:pt-60 sm:pb-40">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('FAQ')],
            ]" />
            <h1 class="mt-4 font-display text-4xl font-bold text-cream">{!! __('Frequently asked :em', ['em' => '<em>' . __('questions') . '</em>']) !!}</h1>
            <p class="mt-4 max-w-2xl text-cream/75">{{ __("Everything we get asked most, organized by trip type. Don't see your question? Just ask us directly.") }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        @foreach ($faqs as $category => $items)
            <div class="mb-12">
                <h2 class="font-display text-2xl font-bold capitalize text-forest">{{ __(ucfirst($category)) }}</h2>
                <div class="mt-4 divide-y divide-forest/10 rounded-2xl border border-forest/10 bg-white shadow-sm">
                    @foreach ($items as $faq)
                        <div x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="flex w-full items-center justify-between px-5 py-4 text-left">
                                <span class="text-sm font-semibold text-forest">{{ $faq->question }}</span>
                                <svg class="h-4 w-4 flex-shrink-0 text-muted transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                            </button>
                            <div x-show="open" x-collapse class="px-5 pb-4 text-sm leading-6 text-muted">
                                {{ $faq->answer }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

    <section class="border-t border-forest/10 bg-cream-2 py-16">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <x-inquiry-form :title="__('Still have questions?')" :description="__('Send us a message and we\'ll get back to you with a straight answer, not a sales pitch.')" />
        </div>
    </section>

@endsection
