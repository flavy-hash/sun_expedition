<?php

namespace App\Support;

use App\Models\Accommodation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Finds the published stay page for a hotel named in an itinerary: by the stay picked in the admin
 * (accommodation_id), or else by an exact name match ("Ahadi Lodge or similar" matches "Ahadi Lodge").
 * Loaded once per request.
 */
class StayDirectory
{
    private ?Collection $byId = null;

    private ?Collection $byName = null;

    public function find(mixed $id = null, ?string $name = null): ?Accommodation
    {
        $this->load();

        if (filled($id) && $stay = $this->byId->get((int) $id)) {
            return $stay;
        }

        return filled($name) ? $this->byName->get(self::normalise($name)) : null;
    }

    public function url(mixed $id = null, ?string $name = null): ?string
    {
        $stay = $this->find($id, $name);

        return $stay ? route('accommodations.show', $stay) : null;
    }

    /** "Ahadi Lodge or similar" → "Ahadi Lodge" */
    public static function displayName(string $name): string
    {
        return Str::of($name)->replaceMatches('/\s+or similar\.?$/i', '')->squish()->toString();
    }

    public static function normalise(string $name): string
    {
        return Str::of($name)->replaceMatches('/\s+or similar\.?$/i', '')->squish()->lower()->toString();
    }

    private function load(): void
    {
        if ($this->byId !== null) {
            return;
        }

        $stays = Accommodation::published()->get(['id', 'name', 'slug']);
        $this->byId = $stays->keyBy('id');
        $this->byName = $stays->keyBy(fn (Accommodation $stay) => self::normalise($stay->name));
    }
}
