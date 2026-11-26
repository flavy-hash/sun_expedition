<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\Tour;
use Database\Seeders\TourSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReviewSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private Tour $tour;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TourSeeder::class);
        $this->tour = Tour::where('slug', 'zanzibar-beach-escape')->firstOrFail();
        Storage::fake('public');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'tour_id' => $this->tour->id,
            'name' => 'Jane Traveler',
            'email' => 'jane@example.com',
            'country' => 'Kenya',
            'rating' => 4,
            'title' => 'Beach days and Stone Town',
            'quote' => 'A wonderful few days, everything was arranged perfectly from start to finish.',
        ], $overrides);
    }

    public function test_review_with_photos_is_stored_pending_and_photos_are_resized_to_webp(): void
    {
        $this->post(route('reviews.store'), $this->payload([
            'images' => [UploadedFile::fake()->image('beach.jpg', 2400, 1800), UploadedFile::fake()->image('dhow.png', 800, 600)],
        ]))->assertRedirect()->assertSessionHas('reviewSubmitted');

        $review = Testimonial::sole();
        $this->assertFalse($review->is_approved);
        $this->assertCount(2, $review->images);

        foreach ($review->images as $path) {
            Storage::disk('public')->assertExists($path);
            $this->assertStringEndsWith('.webp', $path);
        }

        [$width, $height] = getimagesizefromstring(Storage::disk('public')->get($review->images[0]));
        $this->assertSame([1600, 1200], [$width, $height]);
    }

    public function test_pending_reviews_are_hidden_until_approved(): void
    {
        $this->post(route('reviews.store'), $this->payload());
        $review = Testimonial::sole();

        $this->get(route('tours.show', $this->tour))->assertDontSee('Beach days and Stone Town');

        $this->artisan('reviews:approve', ['id' => $review->id])->assertSuccessful();

        $this->get(route('tours.show', $this->tour))->assertSee('Beach days and Stone Town');
    }

    public function test_reviews_page_lists_only_published_reviews_and_filters_by_trip_type(): void
    {
        $kili = Tour::where('slug', 'lemosho-route')->firstOrFail();
        Testimonial::create(['tour_id' => $this->tour->id, 'name' => 'Zara', 'rating' => 5, 'title' => 'Zanzibar bliss', 'quote' => 'Perfect beach days after our safari.', 'is_approved' => true]);
        Testimonial::create(['tour_id' => $kili->id, 'name' => 'Kofi', 'rating' => 4, 'title' => 'Summit at sunrise', 'quote' => 'Hard but the guides were brilliant.', 'is_approved' => true]);
        Testimonial::create(['tour_id' => $kili->id, 'name' => 'Pending Pete', 'rating' => 1, 'title' => 'Not yet approved', 'quote' => 'This should not be visible anywhere.']);

        $this->get(route('reviews.index'))
            ->assertOk()
            ->assertSee('Zanzibar bliss')
            ->assertSee('Summit at sunrise')
            ->assertDontSee('Not yet approved')
            ->assertSee('4.5')
            ->assertSee('Average from 2 reviews');

        $this->get(route('reviews.index', ['category' => 'kilimanjaro']))
            ->assertSee('Summit at sunrise')
            ->assertDontSee('Zanzibar bliss');
    }

    public function test_review_from_the_reviews_page_must_name_a_trip(): void
    {
        $this->post(route('reviews.store'), $this->payload(['tour_id' => null]))
            ->assertSessionHasErrors(['tour_id'], null, 'review');
    }

    public function test_invalid_review_uses_its_own_error_bag(): void
    {
        $this->post(route('reviews.store'), $this->payload([
            'quote' => 'too short',
            'images' => array_fill(0, 5, UploadedFile::fake()->image('x.jpg')),
        ]))->assertSessionHasErrors(['quote', 'images'], null, 'review');

        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_reject_command_deletes_review_and_photos(): void
    {
        $this->post(route('reviews.store'), $this->payload(['images' => [UploadedFile::fake()->image('a.jpg')]]));
        $review = Testimonial::sole();
        $path = $review->images[0];

        $this->artisan('reviews:reject', ['id' => $review->id])->assertSuccessful();

        $this->assertModelMissing($review);
        Storage::disk('public')->assertMissing($path);
    }
}
