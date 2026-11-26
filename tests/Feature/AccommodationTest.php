<?php

namespace Tests\Feature;

use App\Filament\Resources\Accommodations\Pages\ManageAccommodations;
use App\Models\Accommodation;
use App\Models\User;
use Database\Seeders\AccommodationSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccommodationTest extends TestCase
{
    use RefreshDatabase;

    private function stay(array $attributes = []): Accommodation
    {
        return Accommodation::create(array_merge([
            'name' => 'Test Camp',
            'location' => 'Serengeti',
            'type' => 'Tented camp',
            'level' => 'Mid-range',
            'description' => 'Tents near the migration.',
            'is_published' => true,
        ], $attributes));
    }

    public function test_page_lists_published_stays_with_area_chips(): void
    {
        $this->stay(['name' => 'Plains Camp']);
        $this->stay(['name' => 'Beach Villa', 'location' => 'Zanzibar']);
        $this->stay(['name' => 'Secret Lodge', 'is_published' => false]);

        $this->get(route('accommodations.index'))
            ->assertOk()
            ->assertSee('Plains Camp')
            ->assertSee('Beach Villa')
            ->assertDontSee('Secret Lodge')
            ->assertSee(route('accommodations.index', ['location' => 'serengeti']), false)
            ->assertSee(route('accommodations.index', ['location' => 'zanzibar']), false);
    }

    public function test_location_filter_narrows_the_list_and_titles_the_page(): void
    {
        $this->stay(['name' => 'Plains Camp']);
        $this->stay(['name' => 'Beach Villa', 'location' => 'Zanzibar']);

        // The menu previews stay names on every page, so check the listed stays rather than raw HTML.
        $response = $this->get(route('accommodations.index', ['location' => 'zanzibar']))->assertSee('Stays in Zanzibar');
        $this->assertSame(['Beach Villa'], $response->viewData('stays')->pluck('name')->all());
    }

    public function test_unknown_filter_values_show_everything(): void
    {
        $this->stay(['name' => 'Plains Camp']);

        $this->get(route('accommodations.index', ['location' => 'atlantis']))->assertOk()->assertSee('Plains Camp');
        $this->get(route('accommodations.index', ['location' => ['array']]))->assertOk()->assertSee('Plains Camp');
        $this->get(route('reviews.index', ['category' => 'nonsense']))->assertOk();
    }

    public function test_a_new_area_typed_in_the_admin_gets_its_own_chip(): void
    {
        $this->stay(['name' => 'Rift Lodge', 'location' => 'Lake Natron']);

        $this->get(route('accommodations.index'))->assertSee(route('accommodations.index', ['location' => 'lake-natron']), false);
        $this->get(route('accommodations.index', ['location' => 'lake-natron']))->assertSee('Rift Lodge');
    }

    public function test_enquire_prefills_the_contact_message(): void
    {
        $this->get(route('contact', ['stay' => 'Plains Camp']))
            ->assertOk()
            ->assertSee("Hi! I&#039;d like to know more about staying at Plains Camp.", false);
    }

    public function test_seeder_only_runs_on_an_empty_table(): void
    {
        $this->seed(AccommodationSeeder::class);
        $this->assertSame(8, Accommodation::count());

        Accommodation::first()->update(['name' => 'Renamed by admin']);
        $this->seed(AccommodationSeeder::class);

        $this->assertSame(8, Accommodation::count());
        $this->assertDatabaseHas('accommodations', ['name' => 'Renamed by admin']);
    }

    public function test_admin_can_create_edit_and_delete_stays(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/accommodation')->assertOk();

        Livewire::test(ManageAccommodations::class)
            ->callAction('create', ['name' => 'Kilimanjaro Base Hotel', 'location' => 'Moshi', 'type' => 'Lodge', 'level' => 'Budget', 'is_published' => true])
            ->assertHasNoFormErrors();

        $stay = Accommodation::where('name', 'Kilimanjaro Base Hotel')->firstOrFail();
        $this->assertSame('moshi', $stay->location_key);

        Livewire::test(ManageAccommodations::class)
            ->callAction(TestAction::make('edit')->table($stay), ['level' => 'Mid-range'])
            ->assertHasNoFormErrors();
        $this->assertSame('Mid-range', $stay->fresh()->level);

        Livewire::test(ManageAccommodations::class)->callAction(TestAction::make('delete')->table($stay));
        $this->assertModelMissing($stay);
    }

    public function test_editing_keeps_an_external_image_url(): void
    {
        $this->actingAs(User::factory()->create());
        $stay = $this->stay(['image' => 'https://example.com/lodge.jpg']);

        Livewire::test(ManageAccommodations::class)
            ->callAction(TestAction::make('edit')->table($stay), ['name' => 'Renamed Camp'])
            ->assertHasNoFormErrors();

        $this->assertSame(['Renamed Camp', 'https://example.com/lodge.jpg'], [$stay->fresh()->name, $stay->fresh()->image]);
    }
}
