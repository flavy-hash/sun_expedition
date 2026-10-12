<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use Database\Seeders\TourSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TourSeeder::class);
    }

    private function currentTrigger(string $html): ?string
    {
        preg_match_all('/<button[^>]*data-menu-trigger[^>]*aria-controls="mega-([a-z-]+)"[^>]*class="[^"]*is-current/s', $html, $matches);

        return $matches[1][0] ?? null;
    }

    public function test_every_item_has_a_trigger_panel_and_one_preview_per_link(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        foreach (['safari', 'northern-circuit', 'southern-circuit', 'kilimanjaro', 'zanzibar', 'about', 'journal'] as $key) {
            $this->assertStringContainsString("aria-controls=\"mega-{$key}\"", $html);
            $this->assertStringContainsString("id=\"mega-{$key}\" data-mega-panel hidden", $html);
            $this->assertStringContainsString("id=\"preview-{$key}-0\"", $html);

            $links = preg_match_all("/data-mega-target=\"preview-{$key}-(\\d+)\"/", $html, $targets);
            $this->assertGreaterThan(0, $links, "{$key} has child links");
            foreach ($targets[1] as $n) {
                $this->assertStringContainsString("id=\"preview-{$key}-{$n}\"", $html, "{$key} link {$n} has a preview");
            }
        }
    }

    public function test_children_link_to_filtered_pages(): void
    {
        $this->get(route('home'))
            ->assertSee(route('tours.safari', ['tier' => 'classic']), false)
            ->assertSee(route('tours.show', 'northern-circuit-explorer'), false)
            ->assertSee(route('tours.safari'), false);
    }

    public function test_about_dropdown_lists_stays_and_their_busiest_areas(): void
    {
        Accommodation::create(['name' => 'Plains Camp', 'location' => 'Serengeti']);
        Accommodation::create(['name' => 'Ridge Lodge', 'location' => 'Serengeti']);
        Accommodation::create(['name' => 'Beach Villa', 'location' => 'Zanzibar']);

        $this->get(route('home'))
            ->assertSee("Where you&#039;ll stay", false)
            ->assertSee(route('accommodations.index'), false)
            ->assertSee(route('accommodations.index', ['location' => 'serengeti']), false)
            ->assertSee('Stays in Serengeti')
            ->assertSee('Plains Camp and Ridge Lodge');
    }

    public function test_the_current_section_is_marked(): void
    {
        $this->assertNull($this->currentTrigger($this->get(route('home'))->getContent()));
        $this->assertSame('kilimanjaro', $this->currentTrigger($this->get(route('tours.kilimanjaro'))->getContent()));
        $this->assertSame('safari', $this->currentTrigger($this->get(route('tours.safari', ['tier' => 'budget']))->getContent()));
        $this->assertSame('about', $this->currentTrigger($this->get(route('accommodations.index'))->getContent()));
    }

    public function test_package_pages_mark_their_own_section(): void
    {
        $this->assertSame('kilimanjaro', $this->currentTrigger($this->get(route('tours.show', 'lemosho-route'))->getContent()));
        $this->assertSame('northern-circuit', $this->currentTrigger($this->get(route('tours.show', 'northern-circuit-explorer'))->getContent()));
        $this->assertSame('zanzibar', $this->currentTrigger($this->get(route('tours.show', 'zanzibar-beach-escape'))->getContent()));
    }

    public function test_admin_entered_names_are_escaped_in_the_menu(): void
    {
        Accommodation::create(['name' => '<script>alert(1)</script>', 'location' => '<b>Bold</b>']);

        $this->get(route('home'))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }
}
