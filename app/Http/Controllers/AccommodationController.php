<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersListings;
use App\Models\Accommodation;
use Illuminate\Http\Request;

class AccommodationController extends Controller
{
    use FiltersListings;

    public function index(Request $request)
    {
        // Built from the locations actually in use, so a new area added in the admin gets a chip automatically.
        $options = Accommodation::locationOptions();
        $location = $this->activeFilter($request->query('location'), $options);

        $stays = Accommodation::published()->ordered()->get()
            ->when($location, fn ($stays) => $stays->filter(fn (Accommodation $stay) => $stay->location_key === $location)->values());

        return view('accommodations.index', [
            'stays' => $stays,
            'location' => $location ? $options[$location] : null,
            'filterLinks' => $this->filterLinks('accommodations.index', 'location', $options, $location, __('All stays')),
        ]);
    }
}
