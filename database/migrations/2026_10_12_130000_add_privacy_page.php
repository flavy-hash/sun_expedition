<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('pages')->where('slug', 'privacy')->exists()) {
            return;
        }

        // Starting text describing what this website actually collects. Editable in the admin (Content → Privacy policy).
        DB::table('pages')->insert([
            'slug' => 'privacy',
            'title' => 'Privacy',
            'title_highlight' => 'policy',
            'intro' => 'What this website collects, why, and how long we keep it. In short: only what we need to plan your trip, and nothing that tracks you around the web.',
            'body' => implode("\n\n", [
                'Who we are. This website is run by Suni Expedition, a tour operator based in Arusha, Tanzania. For any question about your data, or to ask us to show, correct or delete it, email hello@suniexpedition.co.tz.',
                'Trip requests and quotes. When you send a trip request or ask for an instant quote, we store the details you type in (name, email, phone or WhatsApp number, country, travel dates, number of travelers and your message) so we can reply and prepare your trip. Quotes are emailed to you and to our office as a PDF.',
                'Reviews. If you write a review we store your name, country, rating, text and any photos you attach, plus your email address, which is never published. Photos are resized and their hidden metadata (including GPS location) is removed before they are saved. Reviews only appear on the site after our team has checked them.',
                'Newsletter. If you subscribe, we store your email address only to send you our newsletter. You can ask to be removed at any time.',
                'Anonymous visitor statistics. To understand which pages and trips are useful, we count page views. For each view we record the page, the type of device (mobile, tablet or desktop), the country you are browsing from, and the website that linked to us, if any. We do not use cookies for this and we never store your IP address. Your IP address and browser details are only used, at the moment of the visit, to create a code that groups your page views on that one day; the code changes every day and cannot be turned back into an IP address. The country is looked up from a local copy of the free DB-IP database, so your IP address is not sent anywhere. If your browser sends "Do Not Track" or "Global Privacy Control", nothing is recorded. These statistics are deleted automatically after 180 days.',
                'Third-party services. The language switcher uses Google Translate, which sets a cookie to remember your chosen language. Route maps on trip pages load map tiles from OpenStreetMap (or Mapbox) only when the map comes into view. WhatsApp links open WhatsApp, which has its own privacy policy.',
                'How long we keep data. Trip requests, quotes and reviews are kept for as long as needed to run and follow up on your trip and meet our accounting obligations. Anonymous statistics are kept for 180 days.',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('pages')->where('slug', 'privacy')->delete();
    }
};
