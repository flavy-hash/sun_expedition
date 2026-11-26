@php
    $associations = [
        ['image' => 'images/footer/safaribookings.webp', 'name' => 'SafariBookings'],
        ['image' => 'images/footer/tanzania-national-parks.webp', 'name' => 'Tanzania National Parks (TANAPA)'],
        ['image' => 'images/footer/tanzania-tourist-board.webp', 'name' => 'Tanzania Tourist Board'],
        ['image' => 'images/footer/tato.webp', 'name' => 'Tanzania Association of Tour Operators (TATO)'],
        ['image' => 'images/footer/atta.webp', 'name' => 'African Travel & Tourism Association (ATTA)'],
    ];
@endphp

<footer class="bg-forest-deep pb-[calc(4.5rem+env(safe-area-inset-bottom))] text-cream lg:pb-0">
    <div class="w-full px-5 py-16 sm:px-8 xl:px-5">
        <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr] lg:gap-10">
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-mark.png') }}" alt="Suni Expedition" class="h-14 w-14 rounded-full object-cover">
                    <x-brand-wordmark variant="dark" />
                </div>
                <p class="mt-4 max-w-sm text-sm leading-6 text-cream/70">
                    {!! __('A locally owned Tanzanian operator based in Arusha. No third party brokers  :em safari, climb and beach escape is designed and led by our own guides, who call this land home.', ['em' => '<em class="text-gold not-italic font-semibold">' . __('every') . '</em>']) !!}
                </p>
                <x-social-links variant="dark" class="mt-6" />
            </div>

            <div>
                <h3 class="font-mono text-xs font-semibold uppercase tracking-widest text-cream/60">{{ __('Explore') }}</h3>
                <ul class="mt-4 space-y-2 text-sm text-cream/90">
                    <li><a href="{{ route('tours.safari') }}" class="hover:text-gold">{{ __('Safari') }}</a></li>
                    <li><a href="{{ route('tours.kilimanjaro') }}" class="hover:text-gold">{{ __('Kilimanjaro') }}</a></li>
                    <li><a href="{{ route('tours.zanzibar') }}" class="hover:text-gold">{{ __('Zanzibar') }}</a></li>
                    <li><a href="{{ route('tours.day-trips') }}" class="hover:text-gold">{{ __('Day trips') }}</a></li>
                    <li><a href="{{ route('journal.index') }}" class="hover:text-gold">{{ __('Journal') }}</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-mono text-xs font-semibold uppercase tracking-widest text-cream/60">{{ __('Company') }}</h3>
                <ul class="mt-4 space-y-2 text-sm text-cream/90">
                    <li><a href="{{ route('about') }}" class="hover:text-gold">{{ __('About us') }}</a></li>
                    <li><a href="{{ route('accommodations.index') }}" class="hover:text-gold">{{ __("Where you'll stay") }}</a></li>
                    <li><a href="{{ route('reviews.index') }}" class="hover:text-gold">{{ __('Reviews') }}</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-gold">{{ __('FAQ') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-gold">{{ __('Contact us') }}</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-mono text-xs font-semibold uppercase tracking-widest text-cream/60">{{ __('Get in touch') }}</h3>
                <ul class="mt-4 space-y-3 text-sm text-cream/90">
                    <li>
                        <a href="https://wa.me/{{ config('contact.whatsapp') }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 hover:text-gold">
                            <svg class="h-4 w-4 flex-shrink-0 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.5 7.4L3 20.5l1.6-5.3A8.5 8.5 0 1 1 21 11.5Z" /></svg>
                            {{ __('WhatsApp us') }}
                        </a>
                    </li>
                    <li>
                        <a href="mailto:{{ config('contact.email') }}" class="flex items-center gap-2.5 hover:text-gold">
                            <svg class="h-4 w-4 flex-shrink-0 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m3 7 9 6 9-6" /></svg>
                            {{ config('contact.email') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ config('contact.phone_link') }}" class="flex items-center gap-2.5 hover:text-gold">
                            <svg class="h-4 w-4 flex-shrink-0 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2Z" /></svg>
                            {{ config('contact.phone') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="flex items-center gap-2.5 hover:text-gold">
                            <svg class="h-4 w-4 flex-shrink-0 text-gold rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                            {{ __('Send us a trip request') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-14 border-t border-cream/10 pt-10">
            <div class="flex flex-col items-center gap-6 lg:flex-row lg:justify-between">
                <h3 class="text-center font-mono text-xs font-semibold uppercase tracking-[0.2em] text-cream lg:text-left">{{ __('Proudly associated with') }}</h3>
                <ul class="flex w-full flex-wrap justify-center gap-3 sm:gap-4 lg:w-auto lg:flex-nowrap lg:justify-end">
                    @foreach ($associations as $association)
                        <li class="flex h-16 w-[calc((100%-1.5rem)/3)] items-center justify-center rounded-2xl bg-cream px-3 py-2 sm:h-20 sm:w-36 sm:px-4 lg:w-[9.5rem]">
                            <img src="{{ asset($association['image']) }}" alt="{{ $association['name'] }}" title="{{ $association['name'] }}" loading="lazy" class="max-h-full max-w-full object-contain">
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-8 flex flex-col items-center gap-4 lg:flex-row lg:justify-between">
                <h3 class="text-center font-mono text-xs font-semibold uppercase tracking-[0.2em] text-cream lg:text-left">{{ __('We accept') }}</h3>
                <img src="{{ asset('images/footer/payment-cards.png') }}" alt="{{ __('Visa, Mastercard, American Express and Discover') }}" loading="lazy" class="h-9 w-auto sm:h-10">
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-cream/10 pt-8 text-center font-mono text-[11px] uppercase tracking-wide text-cream/50 sm:flex-row sm:text-left">
            <p>&copy; {{ now()->year }} {{ __('Suni Expedition. All rights reserved.') }}</p>
            <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2">
                <a href="{{ route('privacy') }}" class="hover:text-gold">{{ __('Privacy policy') }}</a>
                <span>{{ __('Arusha, Tanzania') }}</span>
            </div>
        </div>
    </div>
</footer>
