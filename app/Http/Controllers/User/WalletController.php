<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stake;
use App\Models\StakePlan;
use App\Models\User;
use App\Services\StakeRewardService;
use App\Services\UserReserveService;
use App\Services\UserLevelResolver;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WalletController extends Controller
{
    public function index(Request $request, UserReserveService $userReserveService, UserLevelResolver $levelResolver, WalletService $walletService, StakeRewardService $stakeRewardService)
    {
        $user = $request->user();
        $stakeRewardService->creditDueRewardsForUser($user, $walletService);
        $balance = (float) $walletService->getBalance($user);
        $reservedBalance = (float) $userReserveService->getBalance($user);
        $todayEarnings = (float) $user->walletLedgers()
            ->whereIn('type', ['nft_profit', 'chain_income', 'reward_credit'])
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');
        $cumulativeIncome = (float) $user->walletLedgers()
            ->whereIn('type', ['nft_profit', 'chain_income', 'reward_credit'])
            ->sum('amount');
        $level = $levelResolver->resolve($user);
        $canSell = $user->reserves()->where('status', 'confirmed')->exists();
        $recentWalletLedgers = $this->loadRecentWalletLedgers($user);

        return view('wallet.index', [
            'walletBalance' => $balance,
            'reservedBalance' => $reservedBalance,
            'todayEarnings' => $todayEarnings,
            'cumulativeIncome' => $cumulativeIncome,
            'recentWalletLedgers' => $recentWalletLedgers,
            'level' => $level,
            'canSell' => $canSell,
            'plans' => StakePlan::with('requiredLevel')
                ->where('is_active', true)
                ->orderBy('min_amount')
                ->get(),
            'stakes' => Stake::with('stakePlan.requiredLevel')
                ->where('user_id', $user->id)
                ->latest()
                ->get(),
        ]);
    }

    private function loadRecentWalletLedgers(User $user): Collection
    {
        $recentWalletLedgers = $user->walletLedgers()
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $sourceUserIds = $recentWalletLedgers
            ->where('type', 'chain_income')
            ->map(fn ($ledger) => (int) data_get($ledger->meta, 'source_user_id', 0))
            ->filter()
            ->unique()
            ->values();

        if ($sourceUserIds->isEmpty()) {
            return $recentWalletLedgers;
        }

        $sourceUsers = User::query()
            ->with('profile')
            ->whereIn('id', $sourceUserIds->all())
            ->get()
            ->keyBy('id');

        $recentWalletLedgers->each(function ($ledger) use ($sourceUsers) {
            $sourceUserId = (int) data_get($ledger->meta, 'source_user_id', 0);

            if ($sourceUserId > 0) {
                $ledger->setRelation('chainSourceUser', $sourceUsers->get($sourceUserId));
            }
        });

        return $recentWalletLedgers;
    }
}
