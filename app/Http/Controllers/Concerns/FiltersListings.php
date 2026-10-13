<?php

namespace App\Http\Controllers\Concerns;

trait FiltersListings
{
    /**
     * The query value only if it is a known option key, so unknown or stale values simply show everything.
     *
     * @param  array<string, string>  $options  key => label
     */
    protected function activeFilter(mixed $value, array $options): ?string
    {
        return is_string($value) && array_key_exists($value, $options) ? $value : null;
    }

    /**
     * Chips shown above a listing: "All …" first, then one per option.
     *
     * @param  array<string, string>  $options  key => label
     * @return list<array{label: string, url: string, active: bool}>
     */
    protected function filterLinks(string $route, string $param, array $options, ?string $active, string $allLabel): array
    {
        $links = [['label' => $allLabel, 'url' => route($route), 'active' => $active === null]];

        foreach ($options as $key => $label) {
            $links[] = ['label' => $label, 'url' => route($route, [$param => $key]), 'active' => $active === (string) $key];
        }

        return $links;
    }
}
