<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tour;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        $featuredTours = Tour::where('is_featured', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $testimonials = Testimonial::approved()->with('tour')->orderBy('id')->take(3)->get();

        $latestPosts = Post::published()
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('home', compact('featuredTours', 'testimonials', 'latestPosts'));
    }
}
