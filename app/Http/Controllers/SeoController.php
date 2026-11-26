<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Post;
use App\Models\Tour;
use App\Support\Seo;
use Illuminate\Support\Carbon;

class SeoController extends Controller
{
    /** Every public page on the official domain, so search engines can find all packages, stays and articles. */
    public function sitemap()
    {
        $newest = fn ($query) => optional($query->max('updated_at'), fn ($date) => Carbon::parse($date));
        $toursUpdated = $newest(Tour::query());

        $pages = collect([
            ['url' => route('home'), 'lastmod' => $toursUpdated, 'priority' => '1.0'],
            ['url' => route('tours.index'), 'lastmod' => $toursUpdated, 'priority' => '0.9'],
            ['url' => route('tours.safari'), 'lastmod' => $toursUpdated, 'priority' => '0.9'],
            ['url' => route('tours.northern-circuit'), 'lastmod' => $toursUpdated, 'priority' => '0.8'],
            ['url' => route('tours.southern-circuit'), 'lastmod' => $toursUpdated, 'priority' => '0.8'],
            ['url' => route('tours.kilimanjaro'), 'lastmod' => $toursUpdated, 'priority' => '0.9'],
            ['url' => route('tours.zanzibar'), 'lastmod' => $toursUpdated, 'priority' => '0.9'],
            ['url' => route('tours.day-trips'), 'lastmod' => $toursUpdated, 'priority' => '0.8'],
            ['url' => route('accommodations.index'), 'lastmod' => $newest(Accommodation::published()), 'priority' => '0.6'],
            ['url' => route('reviews.index'), 'lastmod' => null, 'priority' => '0.6'],
            ['url' => route('journal.index'), 'lastmod' => $newest(Post::published()), 'priority' => '0.6'],
            ['url' => route('about'), 'lastmod' => null, 'priority' => '0.6'],
            ['url' => route('faq'), 'lastmod' => null, 'priority' => '0.5'],
            ['url' => route('contact'), 'lastmod' => null, 'priority' => '0.6'],
            ['url' => route('privacy'), 'lastmod' => null, 'priority' => '0.2'],
        ])
            ->concat(Tour::orderBy('category')->orderBy('sort_order')->get(['slug', 'updated_at'])
                ->map(fn (Tour $tour) => ['url' => route('tours.show', $tour), 'lastmod' => $tour->updated_at, 'priority' => '0.8']))
            ->concat(Accommodation::published()->ordered()->get(['id', 'slug', 'updated_at'])
                ->map(fn (Accommodation $stay) => ['url' => route('accommodations.show', $stay), 'lastmod' => $stay->updated_at, 'priority' => '0.4']))
            ->concat(Post::published()->orderByDesc('published_at')->get(['slug', 'updated_at'])
                ->map(fn (Post $post) => ['url' => route('journal.show', $post), 'lastmod' => $post->updated_at, 'priority' => '0.5']))
            ->map(fn (array $page) => ['url' => Seo::canonical($page['url'])] + $page);

        return response()->view('seo.sitemap', ['pages' => $pages], 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /csrf-token',
            'Disallow: /quotes/',
            '',
            'Sitemap: ' . Seo::root() . '/sitemap.xml',
        ];

        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
