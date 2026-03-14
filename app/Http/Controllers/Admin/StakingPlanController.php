<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Level;
use App\Models\StakePlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StakingPlanController extends Controller
{
    public function index(): View
    {
        return view('admin.staking-plans.index', [
            'plans' => StakePlan::with('requiredLevel')
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.staking-plans.form', [
            'plan' => new StakePlan(),
            'levels' => Level::query()
                ->orderBy('min_deposit')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePlan($request);
        $plan = StakePlan::create($validated);

        ActivityLog::record('staking.plan.created', $request->user('admin'), $plan);

        return redirect()->route('admin.staking-plans.index')->with('status', 'Plan created.');
    }

    public function edit(StakePlan $stakingPlan): View
    {
        return view('admin.staking-plans.form', [
            'plan' => $stakingPlan,
            'levels' => Level::query()
                ->orderBy('min_deposit')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function update(Request $request, StakePlan $stakingPlan): RedirectResponse
    {
        $validated = $this->validatePlan($request);
        $stakingPlan->update($validated);

        ActivityLog::record('staking.plan.updated', $request->user('admin'), $stakingPlan);

        return redirect()->route('admin.staking-plans.index')->with('status', 'Plan updated.');
    }

    public function destroy(Request $request, StakePlan $stakingPlan): RedirectResponse
    {
        ActivityLog::record('staking.plan.deleted', $request->user('admin'), $stakingPlan);
        $stakingPlan->delete();

        return redirect()->route('admin.staking-plans.index')->with('status', 'Plan deleted.');
    }

    private function validatePlan(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'min:0'],
            'max_payout_multiplier' => ['nullable', 'numeric', 'min:1'],
            'level_required' => ['nullable', 'integer', 'exists:levels,id'],
            'is_active' => ['nullable'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['max_payout_multiplier'] = $validated['max_payout_multiplier'] ?? 2.00;

        return $validated;
    }
}
