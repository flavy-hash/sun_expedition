<?php

namespace App\Support;

use App\Models\Accommodation;
use App\Models\Post;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Builds the mega menu from config/navigation.php, expanding database-driven children (tiers, packages,
 * posts, stay areas) into links that each carry their own preview.
 */
class Navigation
{
    private const TIER_ORDER = ['Budget', 'Classic', 'Mid-range', 'Luxury'];

    private Collection $tours;

    public function __construct(private Request $request)
    {
        $this->tours = Tour::orderBy('sort_order')->get(['id', 'category', 'circuit', 'title', 'slug', 'summary', 'difficulty', 'hero_image']);
    }

    /** @return list<array{key: string, label: string, url: string, title: string, text: string, cta: string, image: ?string, active: bool, children: list<array>}> */
    public function primary(): array
    {
        return collect(config('navigation.primary', []))
            ->map(fn (array $item): array => [
                'key' => $item['key'],
                'label' => __($item['label']),
                'url' => url($item['path']),
                'title' => __($item['title']),
                'text' => __($item['text']),
                'cta' => __($item['cta']),
                'image' => $this->imageUrl($item['image'] ?? null),
                'active' => $this->isActive($item),
                'children' => $this->children($item),
            ])
            ->all();
    }

    private function children(array $item): array
    {
        $source = $item['children'] ?? [];

        if (is_array($source)) {
            return collect($source)
                ->flatMap(fn (array $child): array => ($child['type'] ?? null) === 'stays' ? $this->stayLinks($child['limit'] ?? 3) : [$this->link($child)])
                ->values()
                ->all();
        }

        $links = match (true) {
            $source === 'safari-tiers' => $this->tierLinks(),
            $source === 'journal' => $this->postLinks(),
            Str::startsWith($source, 'circuit:') => $this->tourLinks($this->tours->where('category', 'safari')->where('circuit', Str::after($source, ':'))),
            Str::startsWith($source, 'category:') => $this->tourLinks($this->tours->where('category', Str::after($source, ':'))),
            default => [],
        };

        // "All …" first, previewing the section itself.
        $all = $this->link([
            'label' => $item['all'] ?? 'View all',
            'path' => $item['path'],
            'title' => $item['title'],
            'text' => $item['text'],
            'cta' => $item['cta'],
            'image' => $item['image'] ?? null,
        ]);

        return [$all, ...$links];
    }

    private function tierLinks(): array
    {
        $safaris = $this->tours->where('category', 'safari');

        return $safaris->pluck('difficulty')->filter()->unique()
            ->sortBy(fn (string $tier) => ($index = array_search($tier, self::TIER_ORDER, true)) === false ? PHP_INT_MAX : $index)
            ->map(function (string $tier) use ($safaris): array {
                $matching = $safaris->where('difficulty', $tier);

                return [
                    'label' => __($tier),
                    'url' => route('tours.safari', ['tier' => Str::slug($tier)]),
                    'title' => __(':tier safaris', ['tier' => __($tier)]),
                    'text' => $matching->count() === 1 ? $matching->first()->summary : $matching->pluck('title')->join(', ', ' and '),
                    'cta' => __('See :tier safaris', ['tier' => Str::lower(__($tier))]),
                    'image' => $this->imageUrl($matching->first()->hero_image),
                ];
            })
            ->values()
            ->all();
    }

    private function tourLinks(Collection $tours): array
    {
        return $tours->map(fn (Tour $tour): array => [
            'label' => $tour->title,
            'url' => route('tours.show', $tour),
            'title' => $tour->title,
            'text' => $tour->summary,
            'cta' => __('View package'),
            'image' => $this->imageUrl($tour->hero_image),
        ])->values()->all();
    }

    private function postLinks(): array
    {
        return Post::published()->orderByDesc('published_at')->take(4)->get(['title', 'slug', 'excerpt', 'cover_image'])
            ->map(fn (Post $post): array => [
                'label' => $post->title,
                'url' => route('journal.show', $post),
                'title' => $post->title,
                'text' => $post->excerpt,
                'cta' => __('Read article'),
                'image' => $this->imageUrl($post->cover_image),
            ])->all();
    }

    /** "Where you'll stay" plus the areas with the most published stays. */
    private function stayLinks(int $limit): array
    {
        $stays = Accommodation::published()->ordered()->get(['name', 'location', 'image']);

        $areas = $stays->groupBy(fn (Accommodation $stay) => $stay->location_key)
            ->sortByDesc(fn (Collection $group) => $group->count())
            ->take($limit)
            ->map(fn (Collection $group, string $key): array => [
                'label' => __('Stays in :place', ['place' => $group->first()->location]),
                'url' => route('accommodations.index', ['location' => $key]),
                'title' => __('Stays in :place', ['place' => $group->first()->location]),
                'text' => $group->pluck('name')->join(', ', ' and '),
                'cta' => __('See the stays'),
                'image' => $group->first()->imageUrl(),
            ]);

        return [
            [
                'label' => __("Where you'll stay"),
                'url' => route('accommodations.index'),
                'title' => __("Where you'll stay"),
                'text' => __('The lodges, tented camps and beach hotels we book, chosen for location first.'),
                'cta' => __('Browse all stays'),
                'image' => $stays->first()?->imageUrl() ?? $this->imageUrl('images/stays/serengeti-ridge-lodge.jpg'),
            ],
            ...$areas->values()->all(),
        ];
    }

    private function link(array $child): array
    {
        return [
            'label' => __($child['label']),
            'url' => url($child['path']),
            'title' => __($child['title'] ?? $child['label']),
            'text' => __($child['text'] ?? ''),
            'cta' => __($child['cta'] ?? 'Learn more'),
            'image' => $this->imageUrl($child['image'] ?? null),
        ];
    }

    private function isActive(array $item): bool
    {
        foreach ($item['active'] ?? [] as $segment) {
            if ($this->request->is($segment, $segment . '/*')) {
                return true;
            }
        }

        // Package pages live under /tours/{slug}: highlight the section the package belongs to.
        $tour = $this->request->route('tour');
        if ($tour instanceof Tour) {
            $section = $tour->category === 'safari' && $tour->circuit ? $tour->circuit . '-circuit' : $tour->category;

            return $section === $item['key'];
        }

        return false;
    }

    private function imageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://']) ? $path : asset($path);
    }
}
