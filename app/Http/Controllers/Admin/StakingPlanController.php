<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\StakePlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StakingPlanController extends Controller
{
    public function index()
    {
        return view('admin.staking-plans.index', [
            'plans' => StakePlan::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.staking-plans.form', [
            'plan' => new StakePlan(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePlan($request);
        $plan = StakePlan::create($validated);

        ActivityLog::record('staking.plan.created', $request->user('admin'), $plan);

        return redirect()->route('admin.staking-plans.index')->with('status', 'Plan created.');
    }

    public function edit(StakePlan $stakingPlan)
    {
        return view('admin.staking-plans.form', [
            'plan' => $stakingPlan,
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
            'level_required' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['max_payout_multiplier'] = $validated['max_payout_multiplier'] ?? 2.00;

        return $validated;
    }
}
