<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stake;
use App\Models\StakePlan;
use App\Services\NotificationService;
use App\Services\WalletService;
use App\Services\UserReserveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StakingController extends Controller
{
    public function store(Request $request, WalletService $walletService, NotificationService $notifications, UserReserveService $userReserveService): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:stake_plans,id'],
            'amount' => ['required', 'numeric', 'min:0.0001'],
        ]);

        $plan = StakePlan::where('id', $validated['plan_id'])->where('is_active', true)->firstOrFail();
        $amount = (float) $validated['amount'];

        if ($plan->min_amount && $amount < (float) $plan->min_amount) {
            return back()->withErrors(['amount' => 'Amount is below the minimum.']);
        }

        if ($plan->max_amount && $amount > (float) $plan->max_amount) {
            return back()->withErrors(['amount' => 'Amount exceeds the maximum.']);
        }

        $user = $request->user();
        $balance = (float) $user->walletLedgers()->sum('amount');

        if ($balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        $stake = null;
        DB::transaction(function () use ($user, $plan, $amount, $walletService, $userReserveService, &$stake) {
            $stake = Stake::create([
                'user_id' => $user->id,
                'stake_plan_id' => $plan->id,
                'principal_amount' => $amount,
                'status' => 'active',
                'started_at' => now(),
                'ends_at' => now()->addDays($plan->duration_days),
            ]);

            $walletService->debit(
                $user,
                'stake_lock',
                $amount,
                ['plan_id' => $plan->id],
                $stake
            );

            $userReserveService->creditUserReserve(
                $user,
                $amount,
                'stake_lock',
                'stake',
                $stake->id
            );
        });

        $notifications->notifyUser(
            $user->id,
            'stake_created',
            'Stake created',
            'Your stake has been created successfully.',
            'success',
            ['stake_id' => $stake->id]
        );

        return back()->with('status', 'Stake created.');
    }

    public function unstake(Request $request, Stake $stake, WalletService $walletService, UserReserveService $userReserveService): RedirectResponse
    {
        if ($stake->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($stake->status !== 'active') {
            return back()->withErrors(['stake' => 'Stake is not active.']);
        }

        if (now()->lt($stake->ends_at)) {
            return back()->withErrors(['stake' => 'Stake is still locked.']);
        }

        $plan = $stake->stakePlan;
        $principal = (float) $stake->principal_amount;
        $dailyRate = (float) $plan->daily_rate;
        $days = (int) $plan->duration_days;
        $rawReward = $principal * $dailyRate * $days;

        $maxPayout = $principal * (float) ($plan->max_payout_multiplier ?? 2);
        $maxReward = max(0, $maxPayout - $principal);
        $reward = min($rawReward, $maxReward);

        DB::transaction(function () use ($request, $stake, $principal, $reward, $walletService, $userReserveService) {
            $userReserveService->debitUserReserve(
                $request->user(),
                $principal,
                'stake_unlocked',
                'stake',
                $stake->id
            );

            $walletService->credit($request->user(), 'stake_unlocked', $principal, [], $stake);
            if ($reward > 0) {
                $walletService->credit($request->user(), 'reward_credit', $reward, [], $stake);
            }

            $stake->update([
                'status' => 'completed',
                'total_reward_paid' => $reward,
            ]);
        });

        return back()->with('status', 'Stake completed.');
    }
}
