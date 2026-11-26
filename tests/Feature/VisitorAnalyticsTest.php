<?php

namespace Tests\Feature;

use App\Filament\Resources\PageViews\PageViewResource;
use App\Filament\Widgets\DailyVisitorsChart;
use App\Filament\Widgets\TopToursViewedChart;
use App\Filament\Widgets\TrafficSourcesChart;
use App\Filament\Widgets\VisitorsOverview;
use App\Http\Middleware\LogPageView;
use App\Models\PageView;
use App\Models\Tour;
use App\Models\User;
use Database\Seeders\TourSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class VisitorAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private const BROWSER = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36';

    private const PHONE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Mobile/15E148 Safari/604.1';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TourSeeder::class);
    }

    private function visit(string $url, array $headers = [], string $ip = '197.250.1.10')
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->withHeaders($headers + ['User-Agent' => self::BROWSER])
            ->get($url);
    }

    public function test_a_normal_visit_is_logged_without_storing_the_ip_or_user_agent(): void
    {
        $this->visit(route('tours.show', 'lemosho-route'), ['Referer' => 'https://www.google.com/search?q=lemosho', 'User-Agent' => self::PHONE])->assertOk();

        $view = PageView::sole();
        $this->assertSame('/tours/lemosho-route', $view->path);
        $this->assertSame('package', $view->page_type);
        $this->assertSame(Tour::where('slug', 'lemosho-route')->value('id'), $view->tour_id);
        $this->assertSame('mobile', $view->device);
        $this->assertSame('google.com', $view->referrer_host);
        $this->assertSame(16, strlen($view->visitor_hash));

        $stored = json_encode(DB::table('page_views')->first());
        $this->assertStringNotContainsString('197.250.1.10', $stored);
        $this->assertStringNotContainsString('iPhone', $stored);
        $this->assertFalse(Schema::hasColumn('page_views', 'ip'));
    }

    public function test_internal_referrers_and_page_types_come_from_the_route(): void
    {
        $this->visit(route('accommodations.index'), ['Referer' => url('/')]);

        $view = PageView::sole();
        $this->assertSame('stays', $view->page_type);
        $this->assertNull($view->referrer_host);
        $this->assertSame('desktop', $view->device);
        $this->assertNull($view->tour_id);
    }

    public function test_visits_that_must_not_be_recorded(): void
    {
        $this->visit(route('home'), ['DNT' => '1']);
        $this->visit(route('home'), ['Sec-GPC' => '1']);
        $this->visit(route('home'), ['Sec-Purpose' => 'prefetch;prerender']);
        $this->visit(route('home'), ['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)']);
        $this->visit(route('home'), ['User-Agent' => 'curl/8.4.0']);
        $this->visit(route('home'), ['User-Agent' => '']);
        $this->visit('/admin/login');
        $this->visit('/this-page-does-not-exist')->assertNotFound();
        $this->withHeaders(['User-Agent' => self::BROWSER])->post(route('newsletter.store'), ['email' => 'a@example.com']);

        $this->actingAs(User::factory()->create());
        $this->visit(route('home'));

        $this->assertDatabaseCount('page_views', 0);
    }

    public function test_analytics_can_be_switched_off(): void
    {
        config(['analytics.enabled' => false]);
        $this->visit(route('home'));

        $this->assertDatabaseCount('page_views', 0);
    }

    public function test_visitor_hash_groups_a_day_and_changes_the_next_day(): void
    {
        $today = LogPageView::visitorHash('197.250.1.10', self::BROWSER, '2026-10-12');

        $this->assertSame($today, LogPageView::visitorHash('197.250.1.10', self::BROWSER, '2026-10-12'));
        $this->assertNotSame($today, LogPageView::visitorHash('197.250.1.10', self::BROWSER, '2026-10-13'));
        $this->assertNotSame($today, LogPageView::visitorHash('197.250.1.11', self::BROWSER, '2026-10-12'));

        $this->visit(route('home'));
        $this->visit(route('about'));
        $this->travel(1)->days();
        $this->visit(route('home'));

        $hashes = PageView::orderBy('id')->pluck('visitor_hash');
        $this->assertSame($hashes[0], $hashes[1]);
        $this->assertNotSame($hashes[1], $hashes[2]);
    }

    public function test_country_comes_from_cloudflare_when_present(): void
    {
        $this->visit(route('home'), ['CF-IPCountry' => 'de']);
        $this->visit(route('about'), ['CF-IPCountry' => 'XX'], '10.0.0.5');
        $this->visit(route('faq'), ['CF-IPCountry' => 'T1'], '127.0.0.1');

        $this->assertSame(['DE', null, null], PageView::orderBy('id')->pluck('country')->all());
    }

    public function test_country_comes_from_the_local_database_and_private_ips_are_skipped(): void
    {
        if (! is_file(config('analytics.geoip_database'))) {
            $this->markTestSkipped('Run `php artisan analytics:update-geoip` to download the GeoIP database.');
        }

        $this->visit(route('home'), [], '8.8.8.8');
        $this->visit(route('about'), [], '192.168.1.20');

        $this->assertSame(['US', null], PageView::orderBy('id')->pluck('country')->all());
    }

    public function test_a_database_error_never_breaks_the_page(): void
    {
        Schema::drop('page_views');

        $this->visit(route('home'))->assertOk();
    }

    public function test_old_page_views_are_pruned(): void
    {
        PageView::create(['visitor_hash' => str_repeat('a', 16), 'path' => '/', 'page_type' => 'home', 'device' => 'desktop', 'created_at' => now()->subDays(181)]);
        PageView::create(['visitor_hash' => str_repeat('b', 16), 'path' => '/', 'page_type' => 'home', 'device' => 'desktop', 'created_at' => now()->subDays(10)]);

        $this->artisan('model:prune', ['--model' => [PageView::class]])->assertSuccessful();

        $this->assertSame([str_repeat('b', 16)], PageView::pluck('visitor_hash')->all());
    }

    public function test_admin_insights_screens_render(): void
    {
        $this->visit(route('tours.show', 'lemosho-route'), ['Referer' => 'https://www.tripadvisor.com/x', 'CF-IPCountry' => 'TZ']);
        $this->visit(route('home'));
        $this->actingAs(User::factory()->create());

        $this->get(PageViewResource::getUrl())->assertOk()->assertSee('Lemosho Route')->assertSee('Tanzania')->assertSee('DB-IP');
        $this->get('/admin')->assertOk();

        Livewire::test(VisitorsOverview::class)->assertSee('Visitors today');
        foreach ([DailyVisitorsChart::class, TopToursViewedChart::class, TrafficSourcesChart::class] as $widget) {
            Livewire::test($widget)->assertOk();
        }
        $sources = (fn () => $this->getData())->call(Livewire::test(TrafficSourcesChart::class)->instance());
        $this->assertContains('tripadvisor.com', $sources['labels']);
    }
}
