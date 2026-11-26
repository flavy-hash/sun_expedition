<?php

namespace Tests\Feature;

use App\Models\Tour;
use Database\Seeders\AccommodationSeeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\TourSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private const SITE = 'https://suniexpedition.co.tz';

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => self::SITE]);
        $this->seed([TourSeeder::class, AccommodationSeeder::class, PostSeeder::class]);
    }

    public function test_sitemap_lists_every_public_page_on_the_official_domain(): void
    {
        $response = $this->get('http://old-domain.test/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'The sitemap is valid XML.');
        $urls = collect(iterator_to_array($xml->url, false))->map(fn ($url) => (string) $url->loc);

        $this->assertContains(self::SITE . '/', $urls);
        $this->assertContains(self::SITE . '/tours', $urls);
        $this->assertContains(self::SITE . '/tours/lemosho-route', $urls);
        $this->assertContains(self::SITE . '/accommodations/arusha-garden-lodge', $urls);
        $this->assertTrue($urls->every(fn ($url) => str_starts_with($url, self::SITE . '/')), 'Every URL uses the official domain.');
        $this->assertSame($urls->count(), $urls->unique()->count(), 'No page is listed twice.');
        $this->assertCount(15 + Tour::count() + 8 + \App\Models\Post::published()->count(), $urls);
    }

    public function test_robots_points_search_engines_at_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: ' . self::SITE . '/sitemap.xml')
            ->assertSee('Disallow: /admin');
        $this->assertFileDoesNotExist(public_path('robots.txt'), 'A static robots.txt would be served instead of this one.');
    }

    public function test_pages_name_the_official_domain_as_canonical_and_describe_the_business(): void
    {
        $html = $this->get('http://old-domain.test/tours/lemosho-route?ref=x')->assertOk()->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="' . self::SITE . '/tours/lemosho-route">', $html);
        $this->assertStringContainsString('<meta property="og:url" content="' . self::SITE . '/tours/lemosho-route">', $html);
        $this->assertStringContainsString('<meta property="og:image" content="' . self::SITE . '/images/tours/lemosho.jpg">', $html);
        $this->assertStringContainsString('<title>Lemosho Route — Suni Expedition</title>', $html, 'The brand appears once.');

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $blocks);
        $data = collect($blocks[1])->map(fn ($json) => json_decode($json, true))->keyBy('@type');
        $this->assertSame('Suni Expedition', $data['TravelAgency']['name']);
        $this->assertSame('+255 726 692 072', $data['TravelAgency']['telephone']);
        $this->assertContains('https://www.instagram.com/suniexpedition', $data['TravelAgency']['sameAs']);
        $this->assertSame('Lemosho Route', $data['TouristTrip']['name']);
        $this->assertEquals(Tour::where('slug', 'lemosho-route')->value('price_from'), $data['TouristTrip']['offers']['price']);
    }

    public function test_home_keeps_the_full_tagline(): void
    {
        $this->get('/')->assertSee('<title>Suni Expedition — Tanzania Safaris, Kilimanjaro &amp; Zanzibar</title>', false);
    }

    public function test_search_console_verification_tags_appear_once_configured(): void
    {
        $this->get('/')->assertDontSee('google-site-verification', false);

        config(['services.search_console.google' => 'abc123', 'services.search_console.bing' => 'XYZ789']);

        $this->get('/')
            ->assertSee('<meta name="google-site-verification" content="abc123">', false)
            ->assertSee('<meta name="msvalidate.01" content="XYZ789">', false);
    }

    public function test_other_domains_redirect_permanently_once_enabled(): void
    {
        $this->get('http://sunexpedition.co.tz/tours/lemosho-route?x=1')->assertOk();

        config(['app.canonical_redirect' => true]);

        $this->get('http://sunexpedition.co.tz/tours/lemosho-route?x=1')->assertStatus(301)->assertRedirect(self::SITE . '/tours/lemosho-route?x=1');
        $this->get('http://www.suniexpedition.co.tz/')->assertStatus(301)->assertRedirect(self::SITE . '/');
        $this->get(self::SITE . '/tours')->assertOk();

        // Forms posted to an old domain are not bounced (a redirect would lose the submission).
        Route::post('/_seo-post', fn () => 'saved');
        $this->post('http://sunexpedition.co.tz/_seo-post')->assertOk();
    }
}
