{{-- Behaviour (hover/click/keyboard, live previews, drawer, scroll state) lives in resources/js/menu.js. --}}
<header data-site-header class="site-header fixed inset-x-0 top-0 z-[60]">
    <div class="flex items-center justify-between gap-4 px-[5vw] py-4 xl:px-[3vw] 2xl:px-[5vw] transition-[padding] duration-300 [.scrolled_&]:py-3">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3" aria-label="Suni Expedition — {{ __('Home') }}">
            <img src="{{ asset('images/logo-mark.png') }}" alt="" class="h-14 w-14 rounded-full object-cover shadow-sm">
            <span class="logo-on-hero"><x-brand-wordmark variant="dark" /></span>
            <span class="logo-on-solid"><x-brand-wordmark /></span>
        </a>

        <nav aria-label="{{ __('Main') }}" class="hidden xl:block">
            <ul data-menu-group data-menu-hover class="flex items-center gap-0.5">
                @foreach ($navItems as $item)
                    <li data-menu-item>
                        <button type="button" data-menu-trigger aria-expanded="false" aria-controls="mega-{{ $item['key'] }}"
                                class="menu-trigger flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-2 text-sm font-medium 2xl:px-4 {{ $item['active'] ? 'is-current' : '' }}"
                                @if ($item['active']) aria-current="true" @endif>
                            {{ $item['label'] }}
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="menu-chevron transition" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
                        </button>

                        <div id="mega-{{ $item['key'] }}" data-mega-panel hidden
                             class="absolute left-1/2 top-full w-[min(1180px,94vw)] -translate-x-1/2 pt-2">
                            <div class="grid grid-cols-[minmax(190px,0.75fr)_2.6fr] gap-8 rounded-2xl border border-cream/10 bg-forest-deep p-8 shadow-2xl">
                                <ul class="flex flex-col border-e border-cream/15 pe-6">
                                    @foreach ($item['children'] as $n => $child)
                                        <li>
                                            <a href="{{ $child['url'] }}" data-mega-target="preview-{{ $item['key'] }}-{{ $n + 1 }}"
                                               class="mega-link block py-2.5 text-xs font-semibold uppercase leading-5 tracking-[0.16em] text-cream/85 transition hover:text-gold focus-visible:text-gold">{{ $child['label'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div>
                                    @foreach ([$item, ...$item['children']] as $n => $preview)
                                        <div data-mega-preview id="preview-{{ $item['key'] }}-{{ $n }}" @if ($n > 0) hidden @endif
                                             class="grid grid-cols-[1.6fr_1fr] gap-8">
                                            <div class="flex flex-col justify-center">
                                                <p class="font-serif text-[2rem] leading-tight text-cream">{{ $preview['title'] }}</p>
                                                @if (filled($preview['text']))
                                                    <p class="mt-3 line-clamp-3 max-w-md text-[15px] leading-7 text-cream/75">{{ $preview['text'] }}</p>
                                                @endif
                                                <a href="{{ $preview['url'] }}" tabindex="-1"
                                                   class="mt-7 inline-flex w-fit items-center gap-3 rounded-full border border-cream/40 px-6 py-3 text-xs font-semibold uppercase tracking-[0.16em] text-cream transition hover:border-cream hover:bg-cream/10">
                                                    {{ $preview['cta'] }}
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="rtl:rotate-180" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                                                </a>
                                            </div>
                                            <div class="relative min-h-[220px] overflow-hidden rounded-xl bg-forest">
                                                @if ($preview['image'])
                                                    <img src="{{ $preview['image'] }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ route('contact') }}" class="cta-gradient hidden shrink-0 items-center gap-2 whitespace-nowrap rounded-full px-5 py-2.5 text-sm font-semibold text-[#241206] shadow-[0_10px_30px_rgba(217,96,15,0.35)] transition hover:-translate-y-0.5 sm:inline-flex">
                {{ __('Plan my trip') }}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="rtl:rotate-180" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
            </a>
            {{-- Tablets and small laptops (1024–1279px). Phones use the bottom bar's Menu button. --}}
            <button type="button" data-drawer-open aria-controls="site-drawer" aria-expanded="false" aria-label="{{ __('Open menu') }}"
                    class="hidden h-10 w-10 items-center justify-center rounded-full border border-current/20 bg-current/5 lg:flex xl:hidden">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
            </button>
        </div>
    </div>
</header>

{{-- Slide-in drawer below 1280px: the same items as accordions. --}}
<div id="site-drawer" data-drawer hidden class="fixed inset-0 z-[90] xl:hidden">
    <div data-drawer-close class="drawer-backdrop absolute inset-0 bg-forest-deep/60 opacity-0 transition-opacity duration-300"></div>
    <aside role="dialog" aria-modal="true" aria-label="{{ __('Menu') }}"
           class="drawer-panel absolute inset-y-0 right-0 flex w-[min(24rem,90vw)] translate-x-full flex-col bg-cream shadow-2xl transition-transform duration-300">
        <div class="flex items-center justify-between border-b border-forest/10 px-5 py-4">
            <x-brand-wordmark />
            <button type="button" data-drawer-close aria-label="{{ __('Close menu') }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-forest/15 text-forest">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" /></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-3">
            @foreach ($navItems as $item)
                <details class="group border-b border-forest/10" @if ($item['active']) open @endif>
                    <summary class="flex cursor-pointer list-none items-center justify-between py-3.5 text-sm font-semibold [&::-webkit-details-marker]:hidden {{ $item['active'] ? 'text-orange' : 'text-forest' }}">
                        {{ $item['label'] }}
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition group-open:rotate-180" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
                    </summary>
                    <ul class="flex flex-col pb-3 pl-2">
                        @foreach ($item['children'] as $child)
                            <li><a href="{{ $child['url'] }}" class="block rounded-lg px-2 py-2 text-sm text-muted hover:bg-forest/5 hover:text-forest">{{ $child['label'] }}</a></li>
                        @endforeach
                    </ul>
                </details>
            @endforeach
        </div>

        <div class="border-t border-forest/10 p-5" style="padding-bottom: calc(1.25rem + env(safe-area-inset-bottom))">
            <a href="{{ route('contact') }}" class="cta-gradient flex items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-semibold text-[#241206]">
                {{ __('Plan my trip') }}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="rtl:rotate-180" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
            </a>
        </div>
    </aside>
</div>
