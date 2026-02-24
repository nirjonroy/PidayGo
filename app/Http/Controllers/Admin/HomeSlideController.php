<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\HomeSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeSlideController extends Controller
{
    public function index(): View
    {
        return view('admin.home-slides.index', [
            'slides' => HomeSlide::orderBy('sort_order')->orderByDesc('updated_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.home-slides.form', [
            'slide' => new HomeSlide(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request, true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $slide = new HomeSlide($validated);

        if ($request->hasFile('image')) {
            $slide->image_path = $request->file('image')->store('slides', 'public');
        }

        $slide->save();
        ActivityLog::record('home.slide.created', $request->user('admin'), $slide);

        return redirect()->route('admin.home-slides.index')->with('status', 'Slide created.');
    }

    public function edit(HomeSlide $homeSlide): View
    {
        return view('admin.home-slides.form', [
            'slide' => $homeSlide,
        ]);
    }

    public function update(Request $request, HomeSlide $homeSlide): RedirectResponse
    {
        $validated = $this->validatePayload($request, false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $homeSlide->fill($validated);

        if ($request->hasFile('image')) {
            if ($homeSlide->image_path && Storage::disk('public')->exists($homeSlide->image_path)) {
                Storage::disk('public')->delete($homeSlide->image_path);
            }
            $homeSlide->image_path = $request->file('image')->store('slides', 'public');
        }

        $homeSlide->save();
        ActivityLog::record('home.slide.updated', $request->user('admin'), $homeSlide);

        return redirect()->route('admin.home-slides.index')->with('status', 'Slide updated.');
    }

    public function toggle(Request $request, HomeSlide $homeSlide): RedirectResponse
    {
        $homeSlide->update(['is_active' => !$homeSlide->is_active]);
        ActivityLog::record('home.slide.toggled', $request->user('admin'), $homeSlide);

        return back()->with('status', 'Slide status updated.');
    }

    public function destroy(Request $request, HomeSlide $homeSlide): RedirectResponse
    {
        if ($homeSlide->image_path && Storage::disk('public')->exists($homeSlide->image_path)) {
            Storage::disk('public')->delete($homeSlide->image_path);
        }

        $homeSlide->delete();
        ActivityLog::record('home.slide.deleted', $request->user('admin'), $homeSlide);

        return back()->with('status', 'Slide deleted.');
    }

    private function validatePayload(Request $request, bool $requireImage): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'button_text' => ['nullable', 'string', 'max:50'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => array_filter([
                $requireImage ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ]),
        ]);
    }
}
