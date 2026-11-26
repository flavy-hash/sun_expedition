<?php

namespace Tests\Feature;

use App\Filament\Resources\AboutPages\AboutPageResource;
use App\Filament\Resources\AboutPages\Pages\EditAboutPage;
use App\Filament\Resources\KilimanjaroPackages\Pages\CreateKilimanjaroPackage;
use App\Filament\Resources\KilimanjaroPackages\Pages\ListKilimanjaroPackages;
use App\Filament\Resources\NorthernCircuitSafaris\NorthernCircuitSafariResource;
use App\Filament\Resources\NorthernCircuitSafaris\Pages\CreateNorthernCircuitSafari;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\ZanzibarPackages\Pages\EditZanzibarPackage;
use App\Filament\Resources\ZanzibarPackages\ZanzibarPackageResource;
use App\Filament\Widgets\LatestTripRequests;
use App\Filament\Widgets\PopularPackagesChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TripRequestsChart;
use App\Models\Inquiry;
use App\Filament\Resources\Inquiries\InquiryResource;
use App\Filament\Resources\Inquiries\Pages\ListInquiries;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Tour;
use App\Models\User;
use Database\Seeders\TourSeeder;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TourSeeder::class);
    }

    private function packageData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Rongai Route',
            'slug' => 'rongai-route',
            'summary' => 'The quiet northern approach to Uhuru Peak.',
            'description' => 'Starting near the Kenyan border, Rongai is the driest and least crowded route.',
            'price_from' => 2050,
            'duration_days' => 7,
            'difficulty' => 'Moderate',
            'sort_order' => 4,
        ], $overrides);
    }

    public function test_guests_are_sent_to_the_admin_login(): void
    {
        $this->get('/admin/kilimanjaro')->assertRedirect('/admin/login');
    }

    public function test_every_admin_section_loads(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['/admin', '/admin/northern-circuit', '/admin/southern-circuit', '/admin/kilimanjaro', '/admin/zanzibar', '/admin/journal'] as $url) {
            $this->get($url)->assertOk();
        }

        // Built with the same URL generator the admin's own Edit links use.
        $zanzibar = Tour::where('slug', 'zanzibar-beach-escape')->firstOrFail();
        $this->get(ZanzibarPackageResource::getUrl('edit', ['record' => $zanzibar]))->assertOk();
        $this->get(PostResource::getUrl('create'))->assertOk();
        $this->get(AboutPageResource::getNavigationUrl())->assertOk();
    }

    public function test_dashboard_widgets_report_real_numbers(): void
    {
        $this->actingAs(User::factory()->create());
        $lemosho = Tour::where('slug', 'lemosho-route')->firstOrFail();
        Inquiry::create(['name' => 'Ana', 'email' => 'ana@example.com', 'tour_id' => $lemosho->id]);
        Inquiry::create(['name' => 'Ben', 'email' => 'ben@example.com', 'tour_id' => $lemosho->id]);
        Inquiry::create(['name' => 'Cleo', 'email' => 'cleo@example.com'])->forceFill(['created_at' => now()->subMonths(3)])->save();

        $this->get('/admin')->assertOk();

        Livewire::test(StatsOverview::class)->assertSee('Trip requests · last 30 days')->assertSee('2 more than the previous 30 days');
        Livewire::test(PopularPackagesChart::class)->assertSee('Lemosho Route');
        Livewire::test(LatestTripRequests::class)->assertCanSeeTableRecords(Inquiry::all());

        $months = Livewire::test(TripRequestsChart::class)->instance();
        $data = (fn () => $this->getData())->call($months);
        $this->assertCount(12, $data['labels']);
        $this->assertSame(3, array_sum($data['datasets'][0]['data']));
        $this->assertSame(2, end($data['datasets'][0]['data']));
    }

    public function test_reviews_can_be_approved_and_deleted_with_their_photos(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('public');
        Storage::disk('public')->put('reviews/a.webp', 'x');
        $review = Testimonial::create(['name' => 'Ana', 'rating' => 5, 'title' => 'Great trip', 'quote' => 'Loved every day of it, would go again.', 'images' => ['reviews/a.webp']]);

        $this->get(ReviewResource::getUrl())->assertOk()->assertSee('Great trip');

        Livewire::test(ListReviews::class)->callTableAction('approve', $review);
        $this->assertTrue($review->fresh()->is_approved);

        Livewire::test(ListReviews::class, ['activeTab' => 'all'])->callTableAction('delete', $review);
        $this->assertModelMissing($review);
        Storage::disk('public')->assertMissing('reviews/a.webp');
    }

    public function test_inquiries_can_be_read_and_marked_contacted(): void
    {
        $this->actingAs(User::factory()->create());
        $inquiry = Inquiry::create(['name' => 'Ana', 'email' => 'ana@example.com', 'phone' => '+44 7700 900123', 'message' => 'Two of us, July.']);

        $this->get(InquiryResource::getUrl())->assertOk()->assertSee('Ana');
        $this->get(InquiryResource::getUrl('view', ['record' => $inquiry]))->assertOk()->assertSee('Two of us, July.')->assertSee('wa.me/447700900123', false);

        Livewire::test(ListInquiries::class)->callTableAction('mark_contacted', $inquiry);
        $this->assertSame('contacted', $inquiry->fresh()->status);
        $this->assertFalse(InquiryResource::canCreate());
    }

    public function test_renaming_a_slug_moves_the_editor_to_the_new_address(): void
    {
        $this->actingAs(User::factory()->create());
        $tour = Tour::where('slug', 'zanzibar-beach-escape')->firstOrFail();

        Livewire::test(EditZanzibarPackage::class, ['record' => $tour->getRouteKey()])
            ->fillForm(['slug' => 'zanzibar-beach-week'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(ZanzibarPackageResource::getUrl('edit', ['record' => $tour->fresh()]));

        $this->get(route('tours.show', 'zanzibar-beach-week'))->assertOk();
    }

    public function test_each_package_section_only_lists_and_opens_its_own_tours(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ListKilimanjaroPackages::class)
            ->assertCanSeeTableRecords(Tour::where('category', 'kilimanjaro')->get())
            ->assertCanNotSeeTableRecords(Tour::where('category', '!=', 'kilimanjaro')->get());

        $zanzibar = Tour::where('slug', 'zanzibar-beach-escape')->firstOrFail();
        $this->get(NorthernCircuitSafariResource::getUrl('edit', ['record' => $zanzibar]))->assertNotFound();
    }

    public function test_creating_a_kilimanjaro_package_with_an_itinerary(): void
    {
        $this->actingAs(User::factory()->create());
        Repeater::fake();

        Livewire::test(CreateKilimanjaroPackage::class)
            ->fillForm($this->packageData([
                'route' => 'rongai',
                'highlights' => [['item' => 'Driest route on the mountain']],
                'itineraryItems' => [
                    ['title' => 'Rongai Gate to Simba Camp', 'description' => 'Through pine forest to the first camp.', 'accommodation' => ['location' => 'Simba Camp', 'meal_plan' => 'Full board']],
                    ['title' => 'Summit day', 'description' => 'Midnight start for Uhuru Peak.'],
                ],
            ]))
            ->call('create')
            ->assertHasNoFormErrors();

        $tour = Tour::where('slug', 'rongai-route')->firstOrFail();
        $this->assertSame('kilimanjaro', $tour->category);
        $this->assertSame('rongai', $tour->route);
        $this->assertNull($tour->circuit);
        $this->assertSame(['Driest route on the mountain'], $tour->highlights);

        $days = $tour->itineraryItems;
        $this->assertSame([1, 2], $days->pluck('day_number')->all());
        $this->assertSame('Simba Camp', $days[0]->accommodation['location']);
        $this->assertNull($days[0]->activity, 'An untouched activity block should be stored as empty.');
        $this->assertNull($days[1]->accommodation);

        $this->get(route('tours.show', $tour))->assertOk()->assertSee('Rongai Gate to Simba Camp')->assertSee('Day 1 Accommodation, Simba Camp');
    }

    public function test_safaris_created_in_a_circuit_section_belong_to_that_circuit(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateNorthernCircuitSafari::class)
            ->fillForm($this->packageData(['title' => 'Lake Natron Walking Safari', 'slug' => 'lake-natron-walking-safari', 'difficulty' => 'Luxury']))
            ->call('create')
            ->assertHasNoFormErrors();

        $tour = Tour::where('slug', 'lake-natron-walking-safari')->firstOrFail();
        $this->assertSame(['safari', 'northern'], [$tour->category, $tour->circuit]);
        $this->get(route('tours.northern-circuit'))->assertSee('Lake Natron Walking Safari');
        $this->get(route('tours.safari', ['tier' => 'luxury']))->assertOk();
    }

    public function test_editing_the_about_page_updates_the_site(): void
    {
        $this->actingAs(User::factory()->create());
        $about = Page::where('slug', 'about')->firstOrFail();

        Livewire::test(EditAboutPage::class, ['record' => $about->getRouteKey()])
            ->fillForm(['title' => 'Born in Arusha,', 'body' => "First paragraph.\n\nSecond paragraph."])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get(route('about'))->assertSee('Born in Arusha,')->assertSee('Second paragraph.');
    }

    public function test_journal_drafts_stay_private_until_published(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Draft about Ruaha',
                'slug' => 'draft-about-ruaha',
                'excerpt' => 'Not ready yet.',
                'body' => 'Work in progress.',
                'author_name' => 'Suni Expedition Team',
                'published_at' => null,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = Post::where('slug', 'draft-about-ruaha')->firstOrFail();
        $this->get(route('journal.show', $post))->assertNotFound();
        $this->get(route('journal.index'))->assertDontSee('Draft about Ruaha');

        $post->update(['published_at' => now()->subMinute()]);
        $this->get(route('journal.show', $post))->assertOk();
    }
}
