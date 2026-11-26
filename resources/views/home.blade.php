@extends('layouts.app')

@section('title', 'Suni Expedition')

@section('content')

    {{-- Hero --}}
    <section class="relative overflow-hidden pt-48 pb-36 sm:pt-60 sm:pb-48" x-data="{ playing: true }">
        <video class="absolute inset-0 h-full w-full object-cover" autoplay muted loop playsinline preload="metadata"
               poster="{{ asset('images/hero/home-video-poster.jpg') }}"
               x-ref="heroVideo"
               x-init="if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { $el.pause(); playing = false; }">
            <source src="{{ asset('video/Tanzania Safari Tours - Serengeti National Park & Zanzibar.mp4') }}" type="video/mp4">
        </video>
        <div class="hero-wash-video absolute inset-0"></div>

        <button type="button" class="video-toggle"
                @click="playing ? $refs.heroVideo.pause() : $refs.heroVideo.play(); playing = !playing"
                :aria-label="playing ? 'Pause background video' : 'Play background video'">
            <svg x-show="playing" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14" /><rect x="14" y="5" width="4" height="14" /></svg>
            <svg x-show="!playing" x-cloak width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z" /></svg>
        </button>

        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-[1.3fr_0.7fr] lg:px-8">
            <div>
                <p class="font-mono text-xs font-semibold uppercase tracking-[0.2em] text-orange">{{ __('Arusha, Tanzania') }}</p>
                <h1 class="mt-5 max-w-2xl font-display text-4xl font-bold leading-tight text-forest sm:text-5xl">
                    {!! __('Safaris, Kilimanjaro and Zanzibar  designed and led by :em', ['em' => '<em>' . __('the people who call this land home.') . '</em>']) !!}
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-7 text-ink/80">
                    {{ __('Suni Expedition is a locally owned operator, not a broker. Every itinerary is built around where the wildlife actually is this week, guided by crews we pay above the standard wage, with honest advice on pacing, routes and what to expect.') }}
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('contact') }}" class="cta-gradient rounded-full px-6 py-3 text-sm font-semibold text-[#241206] shadow-[0_10px_30px_rgba(217,96,15,0.35)] transition hover:-translate-y-0.5">
                        {{ __('Plan my trip') }}
                    </a>
                    <a href="{{ route('tours.index') }}" class="rounded-full border border-forest/25 px-6 py-3 text-sm font-semibold text-forest hover:bg-forest/5">
                        {{ __('Explore all trips') }}
                    </a>
                </div>
            </div>

            <x-spinning-seal class="order-first w-full max-w-[240px] -translate-y-4 sm:max-w-[300px] sm:-translate-y-6 lg:order-none lg:max-w-[340px] lg:-translate-y-10" />
        </div>
    </section>

    {{-- About intro --}}
    <section class="border-t border-forest/10 bg-cream-2 py-16">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <div class="font-mono text-xs font-semibold uppercase tracking-wide text-orange">Who we are</div>
            <h2 class="mt-3 font-display text-3xl font-bold text-forest">A local team, <em>not a booking platform.</em></h2>
            <p class="mt-5 text-muted">
                Suni Expedition is based in Arusha and owned by the people who run it. We don't resell someone else's
                itinerary  our own guides plan every safari, Kilimanjaro climb and Zanzibar escape, lead it on the
                ground, and answer your questions directly. No call centers, no reseller markup, just a small team
                that knows this country well.
            </p>
            <p class="mt-4 text-muted">
                Every itinerary starts with a conversation, not a template — we ask about your fitness, your timeline
                and what you actually want to see, then build the route around that instead of handing you a fixed
                package. It's why trips get adjusted mid-safari when the migration moves or the weather shifts,
                rather than sticking to a plan written months in advance.
            </p>
            <p class="mt-4 text-muted">
                We've run trips across the Northern and Southern Circuits, all three standard Kilimanjaro routes, and
                the length of the Zanzibar coast  and we still sleep in the camps and lodges we recommend before we
                put them on an itinerary.
            </p>
            <a href="{{ route('about') }}" class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-orange hover:text-forest">
                Learn more about us <span class="rtl:rotate-180">&rarr;</span>
            </a>
        </div>
    </section>

    {{-- Why us --}}
    <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="font-mono text-xs font-semibold uppercase tracking-wide text-orange">{{ __('Why choose us') }}</div>
        <h2 class="mt-3 font-display text-3xl font-bold text-forest">{!! __('Why travel with :em', ['em' => '<em>' . __('Suni Expedition') . '</em>']) !!}</h2>
        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-full border border-forest/10 bg-forest/5 text-orange">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="mt-4 font-semibold text-forest">{{ __('No third-party brokers') }}</h3>
                <p class="mt-2 text-sm leading-6 text-muted">{{ __("Every trip is designed and led in-house — you're talking directly to the people running your safari, not a reseller.") }}</p>
            </div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-full border border-forest/10 bg-forest/5 text-zanzibar">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5l2.25-6.75h13.5L21 13.5M3 13.5v5.25A1.5 1.5 0 004.5 20.25h15a1.5 1.5 0 001.5-1.5V13.5M3 13.5h18" /></svg>
                </div>
                <h3 class="mt-4 font-semibold text-forest">{{ __('Routes tuned weekly') }}</h3>
                <p class="mt-2 text-sm leading-6 text-muted">{{ __('Game drives are adjusted around where the migration actually is, not a fixed brochure itinerary.') }}</p>
            </div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-full border border-forest/10 bg-forest/5 text-safari">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-5v5m8-9v9M4.5 20.25h15a1.5 1.5 0 001.5-1.5v-13a1.5 1.5 0 00-1.5-1.5h-15a1.5 1.5 0 00-1.5 1.5v13a1.5 1.5 0 001.5 1.5z" /></svg>
                </div>
                <h3 class="mt-4 font-semibold text-forest">{{ __('Fair wages for our crew') }}</h3>
                <p class="mt-2 text-sm leading-6 text-muted">{{ __('Guides, cooks and porters are paid above the standard rate — happier crews make for a better trip.') }}</p>
            </div>
            <div>
                <div class="flex h-11 w-11 items-center justify-center rounded-full border border-forest/10 bg-forest/5 text-orange">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M15.75 3v1.5M3 18.75h18M3.75 8.25h16.5M3.75 21h16.5a.75.75 0 00.75-.75V6a.75.75 0 00-.75-.75H3.75A.75.75 0 003 6v14.25c0 .414.336.75.75.75z" /></svg>
                </div>
                <h3 class="mt-4 font-semibold text-forest">{{ __('Fast, honest replies') }}</h3>
                <p class="mt-2 text-sm leading-6 text-muted">{{ __('Same or next-day responses, with straight answers on pacing, fitness and what a route is really like.') }}</p>
            </div>
        </div>
    </section>

    {{-- Who we serve --}}
    <section class="border-t border-forest/10 bg-cream-2 py-20">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <div class="mx-auto h-1 w-16 rounded-full bg-orange"></div>
            <h2 class="mt-4 font-display text-3xl font-bold text-forest">Who We Serve</h2>
            <p class="mt-4 text-muted">
                Every trip is planned around who's actually traveling — the pacing, vehicles and camps we choose for
                a family look nothing like what we'd book for a honeymoon or a group of wildlife photographers.
            </p>
        </div>

        @php
            $segments = [
                [
                    'image' => 'images/tours/northern-circuit.jpg',
                    'caption' => 'Serengeti · Open road',
                    'title' => 'Solo Travelers',
                    'body' => "Flexible, safe itineraries with a guide who becomes your main company on the road. You're never awkwardly folded into someone else's group.",
                ],
                [
                    'image' => 'images/tours/tarangire-manyara.jpg',
                    'caption' => 'Tarangire · Family safari',
                    'title' => 'Families',
                    'body' => 'Kid-friendly pacing, family tents or connecting rooms, and guides who know how to keep young travelers engaged between sightings.',
                ],
                [
                    'image' => 'images/serve/honeymooners.jpg',
                    'caption' => 'Sunset · Just the two of you',
                    'title' => 'Honeymooners & Couples',
                    'body' => 'Private vehicles, romantic camps and a Zanzibar finale timed for sunsets, not schedules.',
                ],
                [
                    'image' => 'images/hero/safari.jpg',
                    'caption' => 'Serengeti · Convoy',
                    'title' => 'Groups & Friends',
                    'body' => 'Multi-vehicle convoys with a guaranteed window seat for everyone, and one guide across the whole trip so nobody gets split up.',
                ],
                [
                    'image' => 'images/serve/corporate.jpg',
                    'caption' => 'Group safari · Team trip',
                    'title' => 'Corporate & MICE',
                    'body' => 'Incentive trips and team retreats with logistics handled end to end, from airport pickup to the farewell dinner.',
                ],
                [
                    'image' => 'images/serve/photographers.jpg',
                    'caption' => 'Long lens · First light',
                    'title' => 'Photographers & Wildlife Enthusiasts',
                    'body' => 'Routing timed to light and migration reports, with vehicles built for beanbags and long lenses.',
                ],
            ];
        @endphp

        <div class="mx-auto mt-12 max-w-5xl space-y-14 px-4 sm:px-6 lg:px-8">
            @foreach ($segments as $i => $segment)
                <div class="flex flex-col-reverse gap-6 lg:flex-row lg:items-center lg:gap-12 {{ $i % 2 === 1 ? 'lg:flex-row-reverse' : '' }}">
                    <div class="w-full lg:w-1/2">
                        <div class="relative aspect-[4/3] overflow-hidden rounded-2xl shadow-sm">
                            <img src="{{ asset($segment['image']) }}" alt="{{ $segment['title'] }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-forest-deep/70 via-transparent to-transparent"></div>
                            <span class="absolute bottom-3 left-3 font-mono text-[10px] uppercase tracking-wide text-cream/90">{{ $segment['caption'] }}</span>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2">
                        <h3 class="font-display text-2xl font-bold text-forest">{{ $segment['title'] }}</h3>
                        <p class="mt-3 text-muted">{{ $segment['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Trips & pricing (unified, tabbed) --}}
    @php
        $tripCategories = collect(['safari', 'kilimanjaro', 'zanzibar', 'day-trips'])
            ->filter(fn ($cat) => isset($featuredTours[$cat]) && $featuredTours[$cat]->isNotEmpty());
    @endphp
    @if ($tripCategories->isNotEmpty())
        <section class="border-t border-forest/10 bg-cream-2 py-20" x-data="{ activeCat: '{{ $tripCategories->first() }}' }">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="flex items-center gap-2 font-mono text-xs font-semibold uppercase tracking-wide text-orange">
                            <span class="h-px w-6 bg-orange"></span> {{ __('Trips & pricing') }}
                        </div>
                        <h2 class="mt-3 font-display text-3xl font-bold text-forest">{!! __('Pick your :em', ['em' => '<em>' . __('adventure') . '</em>']) !!}</h2>
                        <p class="mt-3 max-w-xl text-muted">{{ __('All prices are per person, land cost only — park fees, guides, camps and meals on the trip included.') }}</p>
                    </div>

                    @php
                        $tabLabels = ['safari' => __('Safaris'), 'kilimanjaro' => __('Kilimanjaro'), 'zanzibar' => __('Zanzibar'), 'day-trips' => __('Day trips')];                    @endphp
                    <div class="inline-flex w-fit flex-wrap gap-1 rounded-full border border-forest/10 bg-white p-1 shadow-sm">
                        @foreach ($tripCategories as $cat)
                            <button type="button" @click="activeCat = '{{ $cat }}'"
                                    class="rounded-full px-4 py-2 text-sm font-medium transition"
                                    :class="activeCat === '{{ $cat }}' ? 'cta-gradient text-[#241206]' : 'text-forest hover:bg-forest/5'">
                                {{ $tabLabels[$cat] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @foreach ($tripCategories as $cat)
                    <div x-show="activeCat === '{{ $cat }}'" x-cloak
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($featuredTours[$cat]->take(3) as $tour)
                                <x-tour-card :tour="$tour" />
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="mt-10 text-center">
                    <a href="{{ route('tours.index') }}" class="inline-flex items-center gap-2 rounded-full border border-forest/25 px-6 py-3 text-sm font-semibold text-forest hover:bg-forest/5">
                        {{ __('View all :count packages', ['count' => $categoryCounts->sum()]) }} <span class="rtl:rotate-180">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section class="border-t border-forest/10 bg-cream-2 py-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <div class="font-mono text-xs font-semibold uppercase tracking-wide text-orange">{{ __('Field notes') }}</div>
                        <h2 class="mt-3 font-display text-3xl font-bold text-forest">{!! __('What :em say', ['em' => '<em>' . __('travelers') . '</em>']) !!}</h2>
                    </div>
                    <a href="{{ route('reviews.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-orange hover:text-forest">
                        {{ __('Read all reviews') }} <span class="rtl:rotate-180">&rarr;</span>
                    </a>
                </div>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <x-testimonial-card :testimonial="$testimonial" />
                    @endforeach
                </div>

                <x-review-badges class="mt-16" />
            </div>
        </section>
    @endif

    {{-- Latest journal --}}
    @if ($latestPosts->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <h2 class="font-display text-3xl font-bold text-forest">{{ __('From the journal') }}</h2>
                <a href="{{ route('journal.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-orange hover:text-forest">{{ __('Read more') }} <span class="rtl:rotate-180">&rarr;</span></a>
            </div>
            <div class="mt-8 grid gap-6 sm:grid-cols-3">
                @foreach ($latestPosts as $post)
                    <a href="{{ route('journal.show', $post) }}" class="group overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        @if ($post->cover_image)
                            <div class="h-40 overflow-hidden">
                                <img src="{{ asset($post->cover_image) }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            </div>
                        @endif
                        <div class="p-6">
                            <p class="font-mono text-xs font-semibold uppercase tracking-wide text-orange">{{ $post->published_at->format('M j, Y') }}</p>
                            <h3 class="mt-2 font-display text-lg font-bold text-forest group-hover:text-orange">{{ $post->title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-muted">{{ $post->excerpt }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Newsletter --}}
    <section class="newsletter-glow relative overflow-hidden py-24">
        <div class="relative mx-auto max-w-xl px-4 text-center sm:px-6 lg:px-8">
            <p class="font-mono text-xs font-semibold uppercase tracking-[0.2em] text-cream/50">{{ __('Stay updated') }}</p>
            <h2 class="mt-4 font-display text-4xl font-bold text-cream sm:text-5xl">{!! __('New :em, first', ['em' => '<em>' . __('adventures') . '</em>']) !!}</h2>
            <p class="mt-5 text-cream/70">
                {{ __('Occasional notes on new routes, quiet season openings and where the herds are heading. No noise.') }}
            </p>

            @error('session') <p class="mx-auto mt-6 max-w-md rounded-lg bg-orange/15 px-4 py-3 text-sm text-gold" role="alert">{{ $message }}</p> @enderror

            @if (session('subscribed'))
                <p class="mt-8 font-mono text-xs uppercase tracking-wide text-gold">{{ __("You're in — thanks for subscribing.") }}</p>
            @else
                <form method="POST" action="{{ route('newsletter.store') }}" class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    @csrf
                    <label for="newsletter-email" class="sr-only">{{ __('Email') }}</label>
                    <input type="email" name="email" id="newsletter-email" required placeholder="{{ __('Enter your email') }}"
                           class="w-full rounded-full border border-cream/25 bg-cream/10 px-5 py-3 text-sm text-cream placeholder:text-cream/50 focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold sm:w-72">
                    <button type="submit" class="cta-gradient rounded-full px-6 py-3 font-mono text-xs font-semibold uppercase tracking-wide text-[#241206]">
                        {{ __('Subscribe') }}
                    </button>
                </form>
                @error('email')
                    <p class="mt-3 text-xs text-gold">{{ $message }}</p>
                @enderror
            @endif
        </div>
    </section>

    {{-- Follow --}}
    <section class="bg-cream py-24">
        <div class="mx-auto max-w-xl px-4 text-center sm:px-6 lg:px-8">
            <p class="font-mono text-xs font-semibold uppercase tracking-[0.2em] text-orange">{{ __('Follow the journey') }}</p>
            <h2 class="mt-4 font-display text-3xl font-bold text-forest sm:text-4xl">{{ __('See where we are this week') }}</h2>
            <p class="mt-4 text-muted">{{ __('Sightings, camp life and the odd sunset, posted from the field.') }}</p>

            <x-social-links class="mt-8 justify-center gap-4" />
        </div>
    </section>

@endsection
