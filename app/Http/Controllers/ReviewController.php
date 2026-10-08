<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Testimonial;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    private const MAX_IMAGE_EDGE = 1600;

    private const CATEGORIES = ['safari', 'kilimanjaro', 'zanzibar'];

    public function index(Request $request)
    {
        $category = in_array($request->query('category'), self::CATEGORIES, true) ? $request->query('category') : null;

        $reviews = Testimonial::approved()
            ->with('tour')
            ->when($category, fn ($query) => $query->whereHas('tour', fn ($tour) => $tour->where('category', $category)))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('reviews.index', [
            'reviews' => $reviews,
            'category' => $category,
            'reviewCount' => Testimonial::approved()->count(),
            'averageRating' => round((float) Testimonial::approved()->avg('rating'), 1),
            'tours' => Tour::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function store(StoreReviewRequest $request)
    {
        $data = $request->safe()->except('images');

        $data['images'] = collect($request->file('images', []))
            ->map(fn (UploadedFile $file) => $this->storeImage($file))
            ->all();

        $data['is_approved'] = false;

        Testimonial::create($data);

        return back()->with('reviewSubmitted', true);
    }

    // Re-encoding through GD drops all EXIF metadata, so guests' GPS coordinates are never published.
    private function storeImage(UploadedFile $file): string
    {
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        imagepalettetotruecolor($image);

        if ($file->getMimeType() === 'image/jpeg' && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
            $image = match ($orientation) {
                3 => imagerotate($image, 180, 0),
                6 => imagerotate($image, -90, 0),
                8 => imagerotate($image, 90, 0),
                default => $image,
            };
        }

        $width = imagesx($image);
        $height = imagesy($image);
        if (max($width, $height) > self::MAX_IMAGE_EDGE) {
            $image = $width >= $height
                ? imagescale($image, self::MAX_IMAGE_EDGE)
                : imagescale($image, (int) round($width * self::MAX_IMAGE_EDGE / $height), self::MAX_IMAGE_EDGE);
        }

        ob_start();
        imagewebp($image, null, 80);
        $path = 'reviews/' . Str::uuid() . '.webp';
        Storage::disk('public')->put($path, ob_get_clean());

        return $path;
    }
}
