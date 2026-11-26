@extends('layouts.app')

@section('title', 'Contact — Suni Expedition')

@section('content')

    <section class="hero-wash-brand pt-48 pb-32 sm:pt-60 sm:pb-40">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('Contact')],
            ]" />
            <h1 class="mt-4 font-display text-4xl font-bold text-cream">{!! __("Let's plan :em", ['em' => '<em>' . __('your trip') . '</em>']) !!}</h1>
            <p class="mt-4 max-w-2xl text-cream/75">{{ __("Free 20-minute planning calls available. Or just send us the form below and we'll reply within one business day.") }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-1">
                <div>
                    <h2 class="font-mono text-xs font-semibold uppercase tracking-widest text-muted">{{ __('WhatsApp') }}</h2>
                    <a href="https://wa.me/{{ config('contact.whatsapp') }}" target="_blank" rel="noopener" class="text-sm text-orange hover:text-forest">{{ config('contact.phone') }}</a>
                </div>
                <div>
                    <h2 class="font-mono text-xs font-semibold uppercase tracking-widest text-muted">{{ __('Email') }}</h2>
                    <a href="mailto:{{ config('contact.email') }}" class="text-sm text-orange hover:text-forest">{{ config('contact.email') }}</a>
                </div>
                <div>
                    <h2 class="font-mono text-xs font-semibold uppercase tracking-widest text-muted">{{ __('Phone') }}</h2>
                    <a href="{{ config('contact.phone_link') }}" class="text-sm text-orange hover:text-forest">{{ config('contact.phone') }}</a>
                </div>
                <div>
                    <h2 class="font-mono text-xs font-semibold uppercase tracking-widest text-muted">{{ __('Office') }}</h2>
                    <p class="text-sm text-muted">{{ __('Arusha, Tanzania') }}</p>
                </div>
            </div>

            <div id="inquiry-form" class="scroll-mt-28 lg:col-span-2">
                <x-inquiry-form :tours="$tours" :message="$message" />
            </div>
        </div>
    </section>

@endsection
