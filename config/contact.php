<?php

/*
 * Public contact details, used by the header/footer, the WhatsApp buttons, the contact page and quote PDFs.
 * Override any of them in .env without touching the code.
 */

$phone = env('CONTACT_PHONE', '+255 726 692 072');

return [
    // Shown to visitors as written; the call link uses the digits only.
    'phone' => $phone,
    'phone_link' => 'tel:' . preg_replace('/[^\d+]/', '', $phone),

    // International format without "+" or spaces, as wa.me expects.
    'whatsapp' => env('CONTACT_WHATSAPP', '255726692072'),

    'email' => env('CONTACT_EMAIL', 'info@suniexpedition.co.tz'),

    // Where new booking requests are emailed (and instant-quote copies, unless QUOTES_OFFICE_EMAIL is set).
    'bookings_email' => env('BOOKINGS_EMAIL', env('MAIL_FROM_ADDRESS', 'info@suniexpedition.co.tz')),

    // Leave one empty to hide its icon.
    'social' => [
        'instagram' => env('SOCIAL_INSTAGRAM', 'https://www.instagram.com/suniexpedition'),
        'facebook' => env('SOCIAL_FACEBOOK', 'https://www.facebook.com/share/1H7HMWxPN2/'),
        'tiktok' => env('SOCIAL_TIKTOK'),
    ],
];
