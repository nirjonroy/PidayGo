<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Level;
use App\Models\ReservePlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReservePlanController extends Controller
{
    public function index(): View
    {
        return view('admin.reserve-plans.index', [
            'plans' => ReservePlan::with(['level', 'ranges'])
                ->orderBy('level_id')
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

        $plan = DB::transaction(function () use ($validated) {
            $plan = ReservePlan::create($this->buildPlanPayload($validated));
            $this->syncRanges($plan, $validated['ranges']);

            return $plan;
        });

        ActivityLog::record('reserve.plan.created', $request->user('admin'), $plan);

        return redirect()->route('admin.reserve-plans.index')->with('status', 'Reserve plan created.');
    }

    public function edit(ReservePlan $reservePlan): View
    {
        return view('admin.reserve-plans.form', [
            'plan' => $reservePlan->loadMissing('ranges'),
            'levels' => Level::orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, ReservePlan $reservePlan): RedirectResponse
    {
        $validated = $this->validatePayload($request, $reservePlan);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        DB::transaction(function () use ($reservePlan, $validated) {
            $reservePlan->update($this->buildPlanPayload($validated));
            $this->syncRanges($reservePlan, $validated['ranges']);
        });

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
        $ranges = $this->normalizeRanges($request->input('ranges', []));
        $payload = array_merge($request->all(), ['ranges' => $ranges]);

        $validator = Validator::make($payload, [
            'level_id' => ['required', 'exists:levels,id'],
            'profit_min_percent' => ['required', 'numeric', 'min:0'],
            'profit_max_percent' => ['required', 'numeric', 'min:0', 'gte:profit_min_percent'],
            'max_sells' => ['nullable', 'integer', 'min:1', 'required_if:unlock_policy,after_sells'],
            'max_sells_per_day' => ['nullable', 'integer', 'min:1'],
            'unlock_policy' => ['required', Rule::in(['never', 'after_sells', 'manual'])],
            'is_active' => ['nullable', 'boolean'],
            'ranges' => ['required', 'array', 'min:1'],
            'ranges.*.id' => ['nullable', 'integer'],
            'ranges.*.wallet_balance_min' => ['required', 'numeric', 'min:0'],
            'ranges.*.wallet_balance_max' => ['required', 'numeric', 'min:0'],
            'ranges.*.reserve_percentage' => ['required', 'numeric', 'min:0.00000001', 'max:100'],
        ]);

        $validator->after(function ($validator) use ($ranges, $plan) {
            $ownedRangeIds = $plan
                ? $plan->ranges()->pluck('id')->map(fn ($id) => (int) $id)->all()
                : [];

            foreach ($ranges as $index => $range) {
                $min = (float) ($range['wallet_balance_min'] ?? 0);
                $max = (float) ($range['wallet_balance_max'] ?? 0);
                $rangeId = isset($range['id']) && $range['id'] !== null ? (int) $range['id'] : null;

                if ($max < $min) {
                    $validator->errors()->add("ranges.$index.wallet_balance_max", 'The wallet balance to field must be greater than or equal to wallet balance from.');
                }

                if ($plan && $rangeId && !in_array($rangeId, $ownedRangeIds, true)) {
                    $validator->errors()->add("ranges.$index.id", 'Invalid reserve criteria row.');
                }
            }
        });

        $validated = $validator->validate();
        $validated['ranges'] = $ranges;

        return $validated;
    }

    private function normalizeRanges(array $rows): array
    {
        return collect($rows)
            ->map(function ($row) {
                return [
                    'id' => isset($row['id']) && $row['id'] !== '' ? (int) $row['id'] : null,
                    'wallet_balance_min' => $row['wallet_balance_min'] ?? null,
                    'wallet_balance_max' => $row['wallet_balance_max'] ?? null,
                    'reserve_percentage' => $row['reserve_percentage'] ?? null,
                ];
            })
            ->filter(function ($row) {
                return ($row['wallet_balance_min'] !== null && $row['wallet_balance_min'] !== '')
                    || ($row['wallet_balance_max'] !== null && $row['wallet_balance_max'] !== '')
                    || ($row['reserve_percentage'] !== null && $row['reserve_percentage'] !== '');
            })
            ->values()
            ->all();
    }

    private function buildPlanPayload(array $validated): array
    {
        $firstRange = collect($validated['ranges'])
            ->sortBy(function ($range) {
                return sprintf(
                    '%020.8F-%020.8F',
                    (float) ($range['wallet_balance_min'] ?? 0),
                    (float) ($range['wallet_balance_max'] ?? 0)
                );
            })
            ->first();

        return [
            'level_id' => $validated['level_id'],
            'wallet_balance_min' => $firstRange['wallet_balance_min'],
            'wallet_balance_max' => $firstRange['wallet_balance_max'],
            'reserve_amount' => $firstRange['reserve_percentage'],
            'profit_min_percent' => $validated['profit_min_percent'],
            'profit_max_percent' => $validated['profit_max_percent'],
            'max_sells' => $validated['max_sells'] ?? null,
            'max_sells_per_day' => $validated['max_sells_per_day'] ?? null,
            'unlock_policy' => $validated['unlock_policy'],
            'is_active' => $validated['is_active'],
        ];
    }

    private function syncRanges(ReservePlan $plan, array $ranges): void
    {
        $existing = $plan->ranges()->get()->keyBy('id');
        $keptIds = [];

        foreach ($ranges as $range) {
            $payload = [
                'wallet_balance_min' => $range['wallet_balance_min'],
                'wallet_balance_max' => $range['wallet_balance_max'],
                'reserve_percentage' => $range['reserve_percentage'],
            ];

            $rangeId = $range['id'] ?? null;
            if ($rangeId && $existing->has($rangeId)) {
                $existing->get($rangeId)->update($payload);
                $keptIds[] = $rangeId;
                continue;
            }

            $keptIds[] = $plan->ranges()->create($payload)->id;
        }

        $deleteQuery = $plan->ranges();
        if (!empty($keptIds)) {
            $deleteQuery->whereNotIn('id', $keptIds);
        }

        $deleteQuery->delete();
    }
}
