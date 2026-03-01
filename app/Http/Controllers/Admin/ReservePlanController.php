<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Level;
use App\Models\ReservePlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReservePlanController extends Controller
{
    public function index(): View
    {
        return view('admin.reserve-plans.index', [
            'plans' => ReservePlan::with('level')
                ->orderBy('level_id')
                ->orderBy('reserve_amount')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.reserve-plans.form', [
            'plan' => new ReservePlan(),
            'levels' => Level::orderBy('code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request, null);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $plan = ReservePlan::create($validated);
        ActivityLog::record('reserve.plan.created', $request->user('admin'), $plan);

        return redirect()->route('admin.reserve-plans.index')->with('status', 'Reserve plan created.');
    }

    public function edit(ReservePlan $reservePlan): View
    {
        return view('admin.reserve-plans.form', [
            'plan' => $reservePlan,
            'levels' => Level::orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, ReservePlan $reservePlan): RedirectResponse
    {
        $validated = $this->validatePayload($request, $reservePlan);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $reservePlan->update($validated);
        ActivityLog::record('reserve.plan.updated', $request->user('admin'), $reservePlan);

        return redirect()->route('admin.reserve-plans.index')->with('status', 'Reserve plan updated.');
    }

    public function toggle(Request $request, ReservePlan $reservePlan): RedirectResponse
    {
        $reservePlan->update(['is_active' => !$reservePlan->is_active]);
        ActivityLog::record('reserve.plan.toggled', $request->user('admin'), $reservePlan);

        return back()->with('status', 'Reserve plan status updated.');
    }

    public function destroy(Request $request, ReservePlan $reservePlan): RedirectResponse
    {
        $reservePlan->delete();
        ActivityLog::record('reserve.plan.deleted', $request->user('admin'), $reservePlan);

        return back()->with('status', 'Reserve plan deleted.');
    }

    private function validatePayload(Request $request, ?ReservePlan $plan): array
    {
        return $request->validate([
            'level_id' => ['required', 'exists:levels,id'],
            'reserve_amount' => [
                'required',
                'numeric',
                'min:0.00000001',
                Rule::unique('reserve_plans', 'reserve_amount')
                    ->where('level_id', $request->input('level_id'))
                    ->ignore($plan?->id),
            ],
            'profit_min_percent' => ['required', 'numeric', 'min:0'],
            'profit_max_percent' => ['required', 'numeric', 'min:0', 'gte:profit_min_percent'],
            'max_sells' => ['nullable', 'integer', 'min:1', 'required_if:unlock_policy,after_sells'],
            'unlock_policy' => ['required', Rule::in(['never', 'after_sells', 'manual'])],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
