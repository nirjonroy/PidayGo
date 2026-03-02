<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::visible()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9);

        return view('frontend.blog.index', [
            'posts' => $posts,
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::visible()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('frontend.blog.show', [
            'post' => $post,
        ]);
    }
}
