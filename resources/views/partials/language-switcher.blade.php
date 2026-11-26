@php
    $languages = [
        'en' => 'English',
        'zh-CN' => '中文',
        'fr' => 'Français',
        'it' => 'Italiano',
        'es' => 'Español',
        'ar' => 'العربية',
    ];
@endphp

<div x-data="{ open: false, activeLang: 'en' }"
     x-init="activeLang = currentGoogTransLang()"
     @click.outside="open = false" @keydown.escape.window="open = false"
     class="fixed right-4 bottom-[calc(5.5rem+env(safe-area-inset-bottom))] z-[70] lg:right-5 lg:bottom-[94px]">

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="notranslate absolute right-0 bottom-full mb-3 w-48 overflow-hidden rounded-2xl border border-forest/10 bg-white py-2 shadow-2xl" dir="ltr">
        @foreach ($languages as $code => $native)
            <button type="button" @click="setSiteLanguage('{{ $code }}'); open = false"
                    class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm"
                    :class="activeLang === '{{ $code }}' ? 'bg-forest/5 text-orange' : 'text-ink/90 hover:bg-forest/5 hover:text-forest'">
                <span>{{ $native }}</span>
                <span class="font-mono text-[10px] uppercase tracking-wide text-muted">{{ $code }}</span>
            </button>
        @endforeach
    </div>

    <button type="button" @click="open = !open; activeLang = currentGoogTransLang()" aria-label="Change language" :aria-expanded="open.toString()"
            class="notranslate flex h-14 w-14 items-center justify-center rounded-full bg-forest text-cream shadow-[0_10px_30px_rgba(7,31,20,0.45)] transition hover:scale-105">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9" /><path d="M3 12h18" /><path d="M12 3c2.5 2.6 3.8 5.7 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3z" />
        </svg>
    </button>
</div>

<script>
    function currentGoogTransLang() {
        const m = document.cookie.match(/googtrans=\/en\/([a-zA-Z-]+)/);
        return m ? m[1] : 'en';
    }

    function setSiteLanguage(lang) {
        if (lang === 'en') {
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;domain=' + location.hostname + ';';
            location.reload();
            return;
        }

        document.cookie = 'googtrans=/en/' + lang + '; path=/';

        const combo = document.querySelector('select.goog-te-combo');
        if (combo) {
            combo.value = lang;
            combo.dispatchEvent(new Event('change'));
        } else {
            location.reload();
        }
    }
</script>
