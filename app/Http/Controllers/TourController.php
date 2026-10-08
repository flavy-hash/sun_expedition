<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index(Request $request)
    {
        return $this->renderCategory($request->query('category'), $request->query('tier'));
    }

    public function category(Request $request, string $category)
    {
        return $this->renderCategory($category, $request->query('tier'));
    }

    public function circuit(string $circuit)
    {
        return $this->renderCategory('safari', null, $circuit);
    }

    // Tier (Budget, Classic, Mid-range...) is stored in the tour's `difficulty` column; the URL uses its slug.
    protected function renderCategory(?string $category, ?string $tierSlug, ?string $circuit = null)
    {
        $tier = null;
        if ($category && $tierSlug) {
            $tier = Tour::where('category', $category)->distinct()->pluck('difficulty')
                ->first(fn (?string $label) => $label && Str::slug($label) === $tierSlug);
            abort_unless($tier, 404);
        }

        $tours = Tour::when($category, fn ($query) => $query->where('category', $category))
            ->when($tier, fn ($query) => $query->where('difficulty', $tier))
            ->when($circuit, fn ($query) => $query->where('circuit', $circuit))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('tours.index', [
            'tours' => $tours,
            'category' => $category,
            'tier' => $tier,
            'circuit' => $circuit,
        ]);
    }

    public function show(Tour $tour)
    {
        $tour->load(['itineraryItems', 'testimonials' => fn ($query) => $query->approved()->latest()]);

        $relatedTours = Tour::where('category', $tour->category)
            ->where('id', '!=', $tour->id)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('tours.show', compact('tour', 'relatedTours'));
    }
}
