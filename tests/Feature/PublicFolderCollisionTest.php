<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicFolderCollisionTest extends TestCase
{
    /**
     * A real folder in public/ with the same name as a page (e.g. public/reviews and /reviews) makes the web
     * server open the folder instead of the page, which broke the All reviews page once.
     */
    public function test_no_public_folder_shadows_a_page(): void
    {
        $pages = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods(), true))
            ->map(fn ($route) => Str::before(ltrim($route->uri(), '/'), '/'))
            ->filter(fn (string $segment) => $segment !== '' && ! Str::startsWith($segment, '{'))
            ->unique();

        $folders = collect(scandir(public_path()))->filter(fn (string $name) => $name[0] !== '.' && is_dir(public_path($name)));

        // The storage link is meant to be served directly by the web server.
        $clashes = $folders->intersect($pages)->diff(['storage'])->values()->all();

        $this->assertSame([], $clashes, 'These public/ folders hide pages with the same address: ' . implode(', ', $clashes));
    }
}
