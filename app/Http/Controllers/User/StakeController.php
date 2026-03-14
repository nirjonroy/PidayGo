<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Stake;
use App\Models\StakePlan;
use App\Services\StakeRewardService;
use App\Services\UserLevelResolver;
use App\Services\WalletService;
use App\Services\UserReserveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class StakeController extends Controller
{
    public function index(Request $request, WalletService $walletService, StakeRewardService $stakeRewardService): View
    {
        $user = $request->user();
        $stakeRewardService->creditDueRewardsForUser($user, $walletService);
        $recentStakeIncome = $user->walletLedgers()
            ->where('type', 'reward_credit')
            ->where('reference_type', (new Stake())->getMorphClass())
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();
        $stakeReferences = Stake::query()
            ->with('stakePlan')
            ->whereIn('id', $recentStakeIncome->pluck('reference_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        return view('stake.index', [
            'balance' => $walletService->getBalance($user),
            'plans' => StakePlan::with('requiredLevel')
                ->where('is_active', true)
                ->orderBy('min_amount')
                ->get(),
            'stakes' => $user->stakes()
                ->with('stakePlan')
                ->where('status', 'active')
                ->orderByDesc('started_at')
                ->get(),
            'recentStakeIncome' => $recentStakeIncome,
            'stakeReferences' => $stakeReferences,
        ]);
    }

    public function store(
        Request $request,
        WalletService $walletService,
        UserReserveService $userReserveService,
        UserLevelResolver $levelResolver
    ): RedirectResponse
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
            $requiredLevel = $plan->requiredLevel;
            $userLevel = $levelResolver->resolve($request->user());

            if (!$this->meetsLevelRequirement($userLevel, $requiredLevel)) {
                return back()->withErrors(['stake_plan_id' => 'Your level is too low for this plan.'])->withInput();
            }
        }

        try {
            $stake = null;
            DB::transaction(function () use ($request, $plan, $amount, $walletService, $userReserveService, &$stake) {
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

                $userReserveService->creditUserReserve(
                    $request->user(),
                    $amount,
                    'stake_lock',
                    'stake',
                    $stake->id
                );
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['amount' => $exception->getMessage()])->withInput();
        }

        return redirect()->route('stake.index')->with('status', 'Stake created successfully.');
    }

    private function meetsLevelRequirement(?Level $userLevel, ?Level $requiredLevel): bool
    {
        if (!$requiredLevel) {
            return false;
        }

        if (!$userLevel) {
            return false;
        }

        $orderedLevelIds = Level::query()
            ->orderBy('min_deposit')
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $userRank = $orderedLevelIds->search($userLevel->id);
        $requiredRank = $orderedLevelIds->search($requiredLevel->id);

        if ($userRank === false || $requiredRank === false) {
            return false;
        }

        return $userRank >= $requiredRank;
    }
}
