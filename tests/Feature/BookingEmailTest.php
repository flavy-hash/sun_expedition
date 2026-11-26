<?php

namespace Tests\Feature;

use App\Mail\NewInquiryMail;
use App\Models\Inquiry;
use App\Models\Tour;
use App\Models\User;
use Database\Seeders\TourSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingEmailTest extends TestCase
{
    use RefreshDatabase;

    private function book(array $overrides = [])
    {
        $tour = Tour::where('slug', 'northern-circuit-explorer')->first();

        return $this->post(route('inquiries.store'), $overrides + [
            'tour_id' => $tour->id,
            'name' => 'Asha Mwangi',
            'email' => 'asha@example.com',
            'phone' => '+254 712 345 678',
            'preferred_dates' => 'July 2027',
            'group_size' => 4,
            'message' => 'Two adults and two children, first safari.',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TourSeeder::class);
        config(['contact.bookings_email' => 'bookings@sunexpedition.co.tz']);
    }

    public function test_a_booking_is_saved_and_emailed_to_the_office_straight_away(): void
    {
        Mail::fake();

        $this->book()->assertRedirect()->assertSessionHas('inquirySent');

        $inquiry = Inquiry::sole();
        Mail::assertSent(NewInquiryMail::class, function (NewInquiryMail $mail) use ($inquiry) {
            return $mail->hasTo('bookings@sunexpedition.co.tz')
                && $mail->hasReplyTo('asha@example.com')
                && $mail->inquiry->is($inquiry);
        });
        Mail::assertNothingQueued();
    }

    public function test_the_email_lists_everything_the_traveller_entered(): void
    {
        $this->book();

        $html = (new NewInquiryMail(Inquiry::sole()))->render();

        foreach (['Asha Mwangi', 'asha@example.com', '+254 712 345 678', 'Northern Circuit Explorer', 'July 2027', '4 travellers', 'first safari', 'Open in the admin', 'wa.me/254712345678'] as $expected) {
            $this->assertStringContainsString($expected, $html);
        }
        $this->assertStringContainsString('New booking request — Asha Mwangi · Northern Circuit Explorer', (new NewInquiryMail(Inquiry::sole()))->envelope()->subject);
    }

    public function test_admins_get_a_bell_notification(): void
    {
        Mail::fake();
        $admin = User::factory()->create();

        $this->book();

        $this->assertSame(1, $admin->notifications()->count());
        $this->assertStringContainsString('Asha Mwangi', $admin->notifications()->first()->data['title']);
    }

    public function test_a_mail_failure_never_loses_the_booking(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP is down'));

        $this->book()->assertRedirect()->assertSessionHas('inquirySent');

        $this->assertSame(1, Inquiry::count());
    }
}
