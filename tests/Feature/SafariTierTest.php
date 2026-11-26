<?php

namespace Tests\Feature;

use Database\Seeders\TourSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafariTierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TourSeeder::class);
    }

    // The nav lists safari tour titles on every page, so assert on the listed tours rather than raw HTML.
    private function listedSlugs(string $url): array
    {
        return $this->get($url)->assertOk()->viewData('tours')->pluck('slug')->all();
    }

    public function test_tier_page_lists_only_safaris_in_that_tier(): void
    {
        $this->get(route('tours.safari', ['tier' => 'classic']))->assertSee('Classic safaris');
        $this->assertSame(['northern-circuit-explorer'], $this->listedSlugs(route('tours.safari', ['tier' => 'classic'])));
    }

    public function test_unknown_tier_returns_404(): void
    {
        $this->get(route('tours.safari', ['tier' => 'luxury']))->assertNotFound();
    }

    public function test_circuit_pages_list_only_their_own_safaris(): void
    {
        $this->get(route('tours.northern-circuit'))->assertSee('Northern Circuit safaris');

        // Shortest first on circuit pages.
        $this->assertSame(
            ['tarangire-lake-manyara-short-break', 'northern-circuit-explorer'],
            $this->listedSlugs(route('tours.northern-circuit')),
        );
        $this->assertSame(['southern-circuit-wilderness'], $this->listedSlugs(route('tours.southern-circuit')));
    }

    public function test_safari_menu_links_only_to_tiers_that_have_safaris(): void
    {
        $this->get(route('home'))
            ->assertSee(route('tours.safari', ['tier' => 'budget']), false)
            ->assertSee(route('tours.safari', ['tier' => 'mid-range']), false)
            ->assertDontSee(route('tours.safari', ['tier' => 'luxury']), false);
    }
}
