<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('journal.index', compact('posts'));
    }

    public function show(Post $post)
    {
        abort_unless($post->isPublished(), 404);

        $recentPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('journal.show', compact('post', 'recentPosts'));
    }
}
