<?php

namespace App\Support;

use App\Models\ItineraryItem;
use Illuminate\Support\Arr;

/**
 * Turns itinerary days into map points. Works on ItineraryItem models (public page) and on raw
 * repeater state arrays (admin), which share the same keys.
 */
class RouteMap
{
    public const AUTOMATIC = '';

    public const CUSTOM = 'custom';

    public const NONE = 'none';

    /** @return array<string, array{name: string, lat: float, lng: float, match: list<string>}> */
    public static function places(): array
    {
        return config('places', []);
    }

    /**
     * The place a day is pinned at, or null when it has none.
     *
     * @return array{key: string, name: string, lat: float, lng: float}|null
     */
    public static function point(ItineraryItem|array $day): ?array
    {
        $day = self::normalize($day);
        $choice = (string) ($day['map_place'] ?? '');

        if ($choice === self::NONE) {
            return null;
        }

        if ($choice === self::CUSTOM) {
            $lat = $day['map_lat'];
            $lng = $day['map_lng'];
            if (! is_numeric($lat) || ! is_numeric($lng) || abs((float) $lat) > 90 || abs((float) $lng) > 180) {
                return null;
            }

            $name = filled($day['map_name']) ? $day['map_name'] : ($day['title'] ?: 'Custom place');

            return ['key' => sprintf('custom:%.5f,%.5f', $lat, $lng), 'name' => (string) $name, 'lat' => (float) $lat, 'lng' => (float) $lng];
        }

        $key = $choice !== '' && isset(self::places()[$choice])
            ? $choice
            : self::guess($day['title'], $day['image_caption'], $day['stay']);

        return $key ? self::placePoint($key) : null;
    }

    /**
     * Guess a place key from the day title, else the photo caption, else the overnight area.
     * Within one text the place mentioned last wins ("Serengeti to Ngorongoro" → Ngorongoro); when two
     * keywords end at the same position, the longer (more specific) phrase wins.
     */
    public static function guess(?string ...$texts): ?string
    {
        foreach ($texts as $text) {
            if (blank($text)) {
                continue;
            }

            $best = null;
            foreach (self::places() as $key => $place) {
                foreach ($place['match'] as $keyword) {
                    $pattern = '/\b' . str_replace('\ ', '\s+', preg_quote($keyword, '/')) . '\b/iu';
                    if (! preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
                        continue;
                    }

                    // Compare where matches END: "Northern Serengeti" and its inner "Serengeti" end together,
                    // so the longer phrase wins instead of the inner word looking "later".
                    [$matched, $offset] = end($matches[0]);
                    $position = $offset + strlen($matched);
                    $length = mb_strlen($keyword);
                    if ($best === null || $position > $best['position'] || ($position === $best['position'] && $length > $best['length'])) {
                        $best = ['key' => $key, 'position' => $position, 'length' => $length];
                    }
                }
            }

            if ($best) {
                return $best['key'];
            }
        }

        return null;
    }

    /**
     * Ordered stops, with consecutive days at the same place merged ("Day 3-4").
     *
     * @param  iterable<ItineraryItem|array>  $days  in itinerary order
     * @return list<array{label: string, days: list<int>, name: string, lat: float, lng: float}>
     */
    public static function stops(iterable $days): array
    {
        $stops = [];
        $dayNumber = 0;

        foreach ($days as $day) {
            $dayNumber++;
            $point = self::point($day);
            if (! $point) {
                continue;
            }

            $last = array_key_last($stops);
            if ($last !== null && $stops[$last]['key'] === $point['key'] && end($stops[$last]['days']) === $dayNumber - 1) {
                $stops[$last]['days'][] = $dayNumber;
            } else {
                $stops[] = $point + ['days' => [$dayNumber]];
            }
        }

        return array_map(function (array $stop): array {
            $first = $stop['days'][0];
            $lastDay = end($stop['days']);

            return [
                'label' => $first === $lastDay ? "Day {$first}" : "Day {$first}-{$lastDay}",
                'days' => $stop['days'],
                'name' => $stop['name'],
                'lat' => $stop['lat'],
                'lng' => $stop['lng'],
            ];
        }, $stops);
    }

    /** Tile layer settings for the public map: Mapbox when a token is configured, else OpenStreetMap. */
    public static function tiles(): array
    {
        $token = config('services.mapbox.token');

        if (filled($token)) {
            return [
                'url' => 'https://api.mapbox.com/styles/v1/mapbox/outdoors-v12/tiles/512/{z}/{x}/{y}@2x?access_token=' . $token,
                'tileSize' => 512,
                'zoomOffset' => -1,
                'attribution' => '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a> © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            ];
        }

        return [
            'url' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            'tileSize' => 256,
            'zoomOffset' => 0,
            'attribution' => '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        ];
    }

    /** @return array{key: string, name: string, lat: float, lng: float} */
    private static function placePoint(string $key): array
    {
        $place = self::places()[$key];

        return ['key' => $key, 'name' => $place['name'], 'lat' => (float) $place['lat'], 'lng' => (float) $place['lng']];
    }

    /** @return array{title: ?string, image_caption: ?string, stay: ?string, map_place: ?string, map_name: ?string, map_lat: mixed, map_lng: mixed} */
    private static function normalize(ItineraryItem|array $day): array
    {
        $data = $day instanceof ItineraryItem ? $day->attributesToArray() : $day;

        return [
            'title' => $data['title'] ?? null,
            'image_caption' => $data['image_caption'] ?? null,
            'stay' => Arr::get($data, 'accommodation.location'),
            'map_place' => $data['map_place'] ?? null,
            'map_name' => $data['map_name'] ?? null,
            'map_lat' => $data['map_lat'] ?? null,
            'map_lng' => $data['map_lng'] ?? null,
        ];
    }
}
