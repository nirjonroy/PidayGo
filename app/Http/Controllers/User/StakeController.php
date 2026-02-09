<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stake;
use App\Models\StakePlan;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class StakeController extends Controller
{
    public function index(Request $request, WalletService $walletService): View
    {
        $user = $request->user();

        return view('stake.index', [
            'balance' => $walletService->getBalance($user),
            'plans' => StakePlan::where('is_active', true)->orderBy('min_amount')->get(),
            'stakes' => $user->stakes()
                ->with('stakePlan')
                ->where('status', 'active')
                ->orderByDesc('started_at')
                ->get(),
        ]);
    }

    public function store(Request $request, WalletService $walletService): RedirectResponse
    {
        $validated = $request->validate([
            'stake_plan_id' => ['required', 'exists:stake_plans,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);

        $plan = StakePlan::findOrFail($validated['stake_plan_id']);

        if (!$plan->is_active) {
            return back()->withErrors(['stake_plan_id' => 'Selected plan is not active.'])->withInput();
        }

        $amount = (float) $validated['amount'];

        if (!is_null($plan->min_amount) && $amount < (float) $plan->min_amount) {
            return back()->withErrors(['amount' => 'Amount is below the minimum.'])->withInput();
        }

        if (!is_null($plan->max_amount) && $amount > (float) $plan->max_amount) {
            return back()->withErrors(['amount' => 'Amount exceeds the maximum.'])->withInput();
        }

        if (!is_null($plan->level_required)) {
            $userLevel = (int) ($request->user()->level ?? 0);
            if ($userLevel < (int) $plan->level_required) {
                return back()->withErrors(['stake_plan_id' => 'Your level is too low for this plan.'])->withInput();
            }
        }

        try {
            $stake = null;
            DB::transaction(function () use ($request, $plan, $amount, $walletService, &$stake) {
                $stake = Stake::create([
                    'user_id' => $request->user()->id,
                    'stake_plan_id' => $plan->id,
                    'principal_amount' => $amount,
                    'status' => 'active',
                    'started_at' => now(),
                    'ends_at' => now()->addDays($plan->duration_days),
                ]);

                $walletService->debit(
                    $request->user(),
                    'stake_lock',
                    $amount,
                    ['plan_id' => $plan->id],
                    $stake
                );
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['amount' => $exception->getMessage()])->withInput();
        }

        return redirect()->route('stake.index')->with('status', 'Stake created successfully.');
    }
}
