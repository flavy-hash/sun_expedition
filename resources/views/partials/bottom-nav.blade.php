{{-- Phones and small tablets (below 1024px). The footer has matching bottom padding so this never covers content. --}}
@php
    $tab = 'flex flex-col items-center justify-end gap-1 rounded-xl py-1.5 font-mono text-[9.5px] uppercase tracking-wide';
@endphp

<nav aria-label="{{ __('Quick links') }}"
     class="fixed inset-x-0 bottom-0 z-[65] grid grid-cols-5 items-end border-t border-forest/10 bg-cream/95 px-1 pt-2 backdrop-blur-xl lg:hidden"
     style="padding-bottom: calc(6px + env(safe-area-inset-bottom))">
    <a href="{{ route('home') }}" class="{{ $tab }} {{ request()->routeIs('home') ? 'text-orange' : 'text-muted' }}" @if (request()->routeIs('home')) aria-current="page" @endif>
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5M5 10v10h14V10" /></svg>
        {{ __('Home') }}
    </a>
    <a href="{{ route('tours.safari') }}" class="{{ $tab }} {{ request()->is('safari', 'northern-circuit', 'southern-circuit') ? 'text-orange' : 'text-muted' }}">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 13.5 5.25 6.75h13.5L21 13.5M3 13.5v5.25A1.5 1.5 0 0 0 4.5 20.25h15a1.5 1.5 0 0 0 1.5-1.5V13.5M3 13.5h18" /></svg>
        {{ __('Safaris') }}
    </a>
    <a href="https://wa.me/{{ config('contact.whatsapp') }}" target="_blank" rel="noopener" class="{{ $tab }} text-muted" aria-label="{{ __('Chat on WhatsApp') }}">
        <span class="-mt-7 flex h-14 w-14 items-center justify-center rounded-full text-[#052e13] shadow-[0_8px_24px_rgba(31,168,85,0.45)] ring-4 ring-cream" style="background:linear-gradient(135deg,#3fd67a,#1fa855)">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.87.5 3.62 1.44 5.12L2 22l5.13-1.55a9.82 9.82 0 0 0 4.9 1.31h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.1c-.24.68-1.4 1.3-1.93 1.38-.5.08-1.12.11-1.8-.11-.42-.13-.96-.31-1.65-.6-2.9-1.25-4.79-4.16-4.93-4.35-.15-.19-1.18-1.57-1.18-3 0-1.42.75-2.12 1.02-2.41.26-.28.57-.35.77-.35h.55c.18 0 .42-.07.65.5.24.58.81 2 .88 2.14.07.15.12.32.02.51-.1.19-.15.31-.29.48-.15.17-.31.38-.44.51-.15.15-.3.31-.13.6.18.3.8 1.32 1.72 2.14 1.19 1.06 2.19 1.39 2.5 1.54.31.15.5.13.68-.05.19-.18.79-.92.99-1.24.2-.31.41-.26.68-.16.28.1 1.75.83 2.05.98.3.15.5.23.57.35.08.14.08.7-.16 1.38z"/></svg>
        </span>
        {{ __('WhatsApp') }}
    </a>
    <a href="{{ config('contact.phone_link') }}" class="{{ $tab }} text-muted">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 0 0-1.173.417l-.97 1.293a1.125 1.125 0 0 1-1.21.38 12.035 12.035 0 0 1-7.143-7.143 1.125 1.125 0 0 1 .38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z" /></svg>
        {{ __('Call') }}
    </a>
    <button type="button" data-drawer-open aria-controls="site-drawer" aria-expanded="false" class="{{ $tab }} text-muted">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18" /></svg>
        {{ __('Menu') }}
    </button>
</nav>
