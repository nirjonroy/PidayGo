<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        return view('admin.blog-posts.index', [
            'posts' => BlogPost::orderByDesc('published_at')->orderByDesc('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.blog-posts.form', [
            'post' => new BlogPost(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request, true);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['slug'] = $this->makeSlug($validated['slug'] ?? $validated['title']);

        $post = new BlogPost($validated);

        if ($request->hasFile('image')) {
            $post->image_path = $request->file('image')->store('blog', 'public');
        }

        $post->save();
        ActivityLog::record('blog.post.created', $request->user('admin'), $post);

        return redirect()->route('admin.blog-posts.index')->with('status', 'Blog post created.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.form', [
            'post' => $blogPost,
        ]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $validated = $this->validatePayload($request, false);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['slug'] = $this->makeSlug($validated['slug'] ?? $validated['title'], $blogPost);

        $blogPost->fill($validated);

        if ($request->hasFile('image')) {
            if ($blogPost->image_path && Storage::disk('public')->exists($blogPost->image_path)) {
                Storage::disk('public')->delete($blogPost->image_path);
            }
            $blogPost->image_path = $request->file('image')->store('blog', 'public');
        }

        $blogPost->save();
        ActivityLog::record('blog.post.updated', $request->user('admin'), $blogPost);

        return redirect()->route('admin.blog-posts.index')->with('status', 'Blog post updated.');
    }

    public function toggle(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update(['is_active' => !$blogPost->is_active]);
        ActivityLog::record('blog.post.toggled', $request->user('admin'), $blogPost);

        return back()->with('status', 'Blog post status updated.');
    }

    public function destroy(Request $request, BlogPost $blogPost): RedirectResponse
    {
        if ($blogPost->image_path && Storage::disk('public')->exists($blogPost->image_path)) {
            Storage::disk('public')->delete($blogPost->image_path);
        }

        $blogPost->delete();
        ActivityLog::record('blog.post.deleted', $request->user('admin'), $blogPost);

        return back()->with('status', 'Blog post deleted.');
    }

    private function validatePayload(Request $request, bool $requireImage): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220'],
            'category' => ['nullable', 'string', 'max:80'],
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string', 'max:10000'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'image' => array_filter([
                $requireImage ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ]),
        ]);
    }

    private function makeSlug(string $source, ?BlogPost $post = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'post';
        }

        $slug = $base;
        $counter = 2;

        while ($this->slugExists($slug, $post)) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?BlogPost $post = null): bool
    {
        $query = BlogPost::where('slug', $slug);
        if ($post) {
            $query->where('id', '!=', $post->id);
        }

        return $query->exists();
    }
}
