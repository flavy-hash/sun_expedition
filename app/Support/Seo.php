<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Search-engine helpers. Every public URL we hand to Google (canonical tags, sitemap, structured data) uses the
 * domain in APP_URL, so the site is listed once under its official address even if older domains still work.
 */
class Seo
{
    /** The official site root, e.g. https://suniexpedition.co.tz (no trailing slash). */
    public static function root(): string
    {
        return rtrim((string) config('app.url'), '/');
    }

    public static function host(): ?string
    {
        return parse_url(self::root(), PHP_URL_HOST) ?: null;
    }

    /** Any URL on this site, moved onto the official domain; query strings and fragments are dropped. */
    public static function canonical(?string $url = null): string
    {
        $path = parse_url($url ?? url()->current(), PHP_URL_PATH) ?: '/';

        return self::root() . ($path === '/' ? '/' : '/' . ltrim($path, '/'));
    }

    /** "Lemosho Route" → "Lemosho Route — Suni Expedition"; the home page keeps the full tagline. */
    public static function title(?string $page): string
    {
        $brand = config('app.name', 'Suni Expedition');
        $page = trim((string) preg_replace('/\s*—\s*' . preg_quote($brand, '/') . '\s*$/u', '', trim((string) $page)));

        return $page === '' || $page === $brand
            ? "{$brand} — Tanzania Safaris, Kilimanjaro & Zanzibar"
            : "{$page} — {$brand}";
    }

    /** An image path or URL as an absolute URL on the official domain (for og:image and structured data). */
    public static function image(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://']) ? $path : self::root() . '/' . ltrim($path, '/');
    }

    /** The business, as schema.org structured data for Google's knowledge panel and rich results. */
    public static function organization(): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'TravelAgency',
            '@id' => self::root() . '/#organization',
            'name' => config('app.name', 'Suni Expedition'),
            'url' => self::root() . '/',
            'logo' => self::image('images/logo-mark.png'),
            'image' => self::image('images/hero/home.jpg'),
            'description' => 'A locally owned Tanzanian tour operator in Arusha running safaris, Kilimanjaro climbs, Zanzibar holidays and day trips.',
            'telephone' => config('contact.phone'),
            'email' => config('contact.email'),
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Arusha', 'addressCountry' => 'TZ'],
            'areaServed' => 'Tanzania',
            'sameAs' => array_values(array_filter(config('contact.social', []))),
        ]);
    }
}
