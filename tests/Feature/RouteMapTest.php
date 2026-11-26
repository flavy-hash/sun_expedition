<?php

namespace Tests\Feature;

use App\Filament\Resources\KilimanjaroPackages\Pages\EditKilimanjaroPackage;
use App\Models\Accommodation;
use App\Models\Tour;
use App\Models\User;
use App\Support\RouteMap;
use Database\Seeders\ItineraryItemSeeder;
use Database\Seeders\TourSeeder;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RouteMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_place_mentioned_last_wins(): void
    {
        $this->assertSame('ngorongoro', RouteMap::guess('Serengeti to Ngorongoro Crater rim'));
        $this->assertSame('mweka-gate', RouteMap::guess('Mweka Camp to Mweka Gate'));
        $this->assertSame('shira-2', RouteMap::guess('Shira 1 to Shira 2 Camp'));
    }

    public function test_the_longer_more_specific_phrase_wins_at_the_same_spot(): void
    {
        $this->assertSame('northern-serengeti', RouteMap::guess('Game drives in the Northern Serengeti'));
        $this->assertSame('lake-manyara', RouteMap::guess('Afternoon at Lake Manyara'));
    }

    public function test_keywords_match_whole_words_only(): void
    {
        $this->assertNull(RouteMap::guess('Departure via Dartmouth'));
        $this->assertNull(RouteMap::guess('Mosquito-free evening')); // must not match "moshi"
    }

    public function test_falls_back_to_caption_then_overnight_area(): void
    {
        $this->assertSame('nungwi', RouteMap::point(['title' => 'Beach & Departure', 'image_caption' => 'Nungwi Beachfront'])['key']);
        $this->assertSame('karatu', RouteMap::point(['title' => 'Free day', 'accommodation' => ['location' => 'Karatu']])['key']);
        $this->assertNull(RouteMap::point(['title' => 'Departure']));
    }

    public function test_explicit_custom_and_hidden_choices(): void
    {
        $this->assertSame('Uhuru Peak', RouteMap::point(['title' => 'Arrival in Arusha', 'map_place' => 'uhuru-peak'])['name']);
        $this->assertNull(RouteMap::point(['title' => 'Arrival in Arusha', 'map_place' => 'none']));

        $custom = RouteMap::point(['title' => 'Lake Natron camp', 'map_place' => 'custom', 'map_lat' => -2.35, 'map_lng' => 36.05]);
        $this->assertSame([-2.35, 36.05, 'Lake Natron camp'], [$custom['lat'], $custom['lng'], $custom['name']]);
        $this->assertSame('Ol Doinyo Lengai', RouteMap::point(['title' => 'x', 'map_place' => 'custom', 'map_name' => 'Ol Doinyo Lengai', 'map_lat' => -2.76, 'map_lng' => 35.91])['name']);

        $this->assertNull(RouteMap::point(['title' => 'x', 'map_place' => 'custom', 'map_lat' => 120, 'map_lng' => 36]));
        $this->assertNull(RouteMap::point(['title' => 'x', 'map_place' => 'custom', 'map_lat' => null, 'map_lng' => 36]));
    }

    public function test_consecutive_days_at_one_place_merge_into_a_range(): void
    {
        $stops = RouteMap::stops([
            ['title' => 'Arrival in Arusha'],
            ['title' => 'Drive to Tarangire'],
            ['title' => 'Into the Serengeti'],
            ['title' => 'Full day in the Serengeti'],
            ['title' => 'Departure'],
            ['title' => 'Back in the Serengeti'],
        ]);

        $this->assertSame(['Day 1', 'Day 2', 'Day 3-4', 'Day 6'], array_column($stops, 'label'));
        $this->assertSame([3, 4], $stops[2]['days']);
    }

    public function test_package_page_shows_the_map_only_with_two_or_more_stops(): void
    {
        $this->seed([TourSeeder::class, ItineraryItemSeeder::class]);

        $this->get(route('tours.show', 'northern-circuit-explorer'))
            ->assertOk()
            ->assertSee('data-route-map', false)
            ->assertSee('Day 2-3 Central Serengeti (Seronera)');

        // A one-day tour has a single stop: no map.
        $this->get(route('tours.show', 'stone-town-spice-farm-cultural-tour'))->assertOk()->assertDontSee('data-route-map', false);
    }

    public function test_map_location_and_accommodation_picker_save_from_the_admin(): void
    {
        $this->seed([TourSeeder::class, ItineraryItemSeeder::class]);
        $this->actingAs(User::factory()->create());

        $stay = Accommodation::create(['name' => 'Kibo Base Lodge', 'location' => 'Moshi', 'image' => 'images/stays/arusha-garden-lodge.jpg']);
        $tour = Tour::where('slug', 'marangu-route')->firstOrFail();

        $page = Livewire::test(EditKilimanjaroPackage::class, ['record' => $tour->getRouteKey()]);
        // Existing days are keyed by generated ids, in day order.
        [$first, $last] = [array_key_first($page->get('data.itineraryItems')), array_key_last($page->get('data.itineraryItems'))];
        $lastDay = "data.itineraryItems.{$last}.accommodation";

        $page->set("{$lastDay}.location", null)->set("{$lastDay}.accommodation_id", $stay->id);

        $this->assertSame('Kibo Base Lodge', $page->get("{$lastDay}.image_caption"));
        $this->assertSame('Moshi', $page->get("{$lastDay}.location"));
        $this->assertSame(['images/stays/arusha-garden-lodge.jpg'], array_values($page->get("{$lastDay}.image")));

        $page->set("data.itineraryItems.{$first}.map_place", 'custom')
            ->set("data.itineraryItems.{$first}.map_name", 'Rongai trailhead')
            ->set("data.itineraryItems.{$first}.map_lat", -2.98)
            ->set("data.itineraryItems.{$first}.map_lng", 37.5)
            ->call('save')
            ->assertHasNoFormErrors();

        $days = $tour->fresh()->itineraryItems;
        $this->assertSame(['custom', 'Rongai trailhead', -2.98, 37.5], [$days[0]->map_place, $days[0]->map_name, $days[0]->map_lat, $days[0]->map_lng]);
        $this->assertSame(['Kibo Base Lodge', 'images/stays/arusha-garden-lodge.jpg', $stay->id],
            [$days[5]->accommodation['image_caption'], $days[5]->accommodation['image'], (int) $days[5]->accommodation['accommodation_id']]);

        $this->get(route('tours.show', $tour))->assertSee('Day 1 Rongai trailhead');
    }
}
