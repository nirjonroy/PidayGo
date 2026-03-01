<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ChainBonusSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChainBonusSettingController extends Controller
{
    public function index(): View
    {
        return view('admin.chain-bonuses.index', [
            'bonuses' => ChainBonusSetting::orderBy('depth')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.chain-bonuses.form', [
            'bonus' => new ChainBonusSetting(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request, null);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $bonus = ChainBonusSetting::create($validated);
        ActivityLog::record('chain.bonus.created', $request->user('admin'), $bonus);

        return redirect()->route('admin.chain-bonuses.index')->with('status', 'Chain bonus created.');
    }

    public function edit(ChainBonusSetting $chainBonus): View
    {
        return view('admin.chain-bonuses.form', [
            'bonus' => $chainBonus,
        ]);
    }

    public function update(Request $request, ChainBonusSetting $chainBonus): RedirectResponse
    {
        $validated = $this->validatePayload($request, $chainBonus);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $chainBonus->update($validated);
        ActivityLog::record('chain.bonus.updated', $request->user('admin'), $chainBonus);

        return redirect()->route('admin.chain-bonuses.index')->with('status', 'Chain bonus updated.');
    }

    public function toggle(Request $request, ChainBonusSetting $chainBonus): RedirectResponse
    {
        $chainBonus->update(['is_active' => !$chainBonus->is_active]);
        ActivityLog::record('chain.bonus.toggled', $request->user('admin'), $chainBonus);

        return back()->with('status', 'Chain bonus status updated.');
    }

    public function destroy(Request $request, ChainBonusSetting $chainBonus): RedirectResponse
    {
        $chainBonus->delete();
        ActivityLog::record('chain.bonus.deleted', $request->user('admin'), $chainBonus);

        return back()->with('status', 'Chain bonus deleted.');
    }

    private function validatePayload(Request $request, ?ChainBonusSetting $bonus): array
    {
        return $request->validate([
            'depth' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('chain_bonus_settings', 'depth')->ignore($bonus?->id),
            ],
            'percent' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
