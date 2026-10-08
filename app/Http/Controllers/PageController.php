<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Page;
use App\Models\Tour;

class PageController extends Controller
{
    public function about()
    {
        $page = Page::where('slug', 'about')->firstOrFail();

        return view('pages.about', compact('page'));
    }

    public function faq()
    {
        $faqs = Faq::orderBy('category')->orderBy('sort_order')->get()->groupBy('category');

        return view('pages.faq', compact('faqs'));
    }

    public function contact()
    {
        $tours = Tour::orderBy('title')->get(['id', 'title']);

        return view('pages.contact', compact('tours'));
    }
}
