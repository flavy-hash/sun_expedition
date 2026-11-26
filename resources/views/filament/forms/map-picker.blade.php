@php
    use App\Support\RouteMap;
    use Illuminate\Support\Str;

    // This field lives inside one itinerary day: data.itineraryItems.{uuid}.map_picker
    $itemPath = Str::beforeLast($getStatePath(), '.');
    $itemKey = Str::afterLast($itemPath, '.');
    $days = data_get($getLivewire(), 'data.itineraryItems') ?? [];
    $order = array_keys($days);
    $position = array_search($itemKey, $order, true);

    $point = RouteMap::point($days[$itemKey] ?? []);
    $others = collect($days)
        ->except($itemKey)
        ->map(fn ($day, $key) => ['day' => array_search($key, $order, true) + 1, 'point' => RouteMap::point($day)])
        ->filter(fn ($day) => $day['point'])
        ->map(fn ($day) => ['day' => $day['day'], 'lat' => $day['point']['lat'], 'lng' => $day['point']['lng'], 'name' => $day['point']['name']])
        ->values()
        ->all();

    $config = [
        'itemPath' => $itemPath,
        'day' => $position === false ? null : $position + 1,
        'point' => $point ? ['lat' => $point['lat'], 'lng' => $point['lng'], 'name' => $point['name']] : null,
        'others' => $others,
    ];
@endphp

{{-- The key changes with the point and the other stops, so Livewire re-initialises the map when they change.
     Inline styles: the admin uses Filament's stylesheet, which doesn't contain the site's Tailwind utilities. --}}
<div wire:key="map-picker-{{ md5($itemPath . json_encode([$config['point'], $others])) }}"
     x-data="sunMapPicker(@js($config))">
    <div wire:ignore x-ref="map" style="height: 16rem; width: 100%; overflow: hidden; border-radius: 0.5rem; border: 1px solid rgb(127 127 127 / 0.25);"></div>
    <p class="fi-fo-field-wrp-helper-text fi-sc-text" style="margin-top: 0.5rem; font-size: 0.75rem; opacity: 0.75;">
        Click the map or drag the orange pin to set an exact spot (this switches the day to "Other place"). Grey dots are the other days.
    </p>
</div>
