<?php

return [
    // Anonymous, cookie-free page view statistics (see App\Http\Middleware\LogPageView).
    'enabled' => env('ANALYTICS_ENABLED', true),

    // Page views older than this are deleted by the daily `model:prune` run.
    'retention_days' => (int) env('ANALYTICS_RETENTION_DAYS', 180),

    // Local copy of DB-IP's free "IP to Country Lite" database, refreshed monthly by `analytics:update-geoip`.
    'geoip_database' => storage_path('app/geoip/dbip-country-lite.mmdb'),

    // Paths that are never logged (admin, Livewire, files, signed downloads…).
    'excluded_paths' => ['up', 'admin', 'admin/*', 'livewire*', 'filament/*', 'storage/*', 'uploads/*', 'images/*', 'build/*', 'vendor/*', 'quotes/*'],

    // Route name => page type shown in the admin. Unlisted routes use their route name.
    'page_types' => [
        'home' => 'home',
        'tours.index' => 'all trips',
        'tours.safari' => 'safaris',
        'tours.northern-circuit' => 'safaris',
        'tours.southern-circuit' => 'safaris',
        'tours.kilimanjaro' => 'kilimanjaro',
        'tours.zanzibar' => 'zanzibar',
        'tours.show' => 'package',
        'accommodations.index' => 'stays',
        'reviews.index' => 'reviews',
        'journal.index' => 'blog',
        'journal.show' => 'blog post',
        'about' => 'about',
        'faq' => 'faq',
        'contact' => 'contact',
        'privacy' => 'privacy',
    ],
];
