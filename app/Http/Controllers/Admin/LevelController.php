<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LevelController extends Controller
{
    public function index(): View
    {
        return view('admin.levels.index', [
            'levels' => Level::orderBy('min_reservation')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.levels.form', [
            'level' => new Level(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayload($request);
        Level::create($data);

        return redirect()->route('admin.levels.index')->with('status', 'Level created.');
    }

    public function edit(Level $level): View
    {
        return view('admin.levels.form', [
            'level' => $level,
        ]);
    }

    public function update(Request $request, Level $level): RedirectResponse
    {
        $data = $this->validatePayload($request);
        $level->update($data);

        return redirect()->route('admin.levels.index')->with('status', 'Level updated.');
    }

    public function toggle(Level $level): RedirectResponse
    {
        $level->update([
            'is_active' => !$level->is_active,
        ]);

        return back()->with('status', 'Level status updated.');
    }

    private function validatePayload(Request $request): array
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'min_reservation' => ['required', 'numeric', 'min:0'],
            'max_reservation' => ['required', 'numeric', 'gte:min_reservation'],
            'income_min_percent' => ['required', 'numeric', 'min:0'],
            'income_max_percent' => ['required', 'numeric', 'gte:income_min_percent'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        return $validated;
    }
}
