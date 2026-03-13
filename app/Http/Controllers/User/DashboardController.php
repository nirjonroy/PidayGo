<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\StakeRewardService;
use App\Services\UserLevelResolver;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const INCOME_LEDGER_TYPES = [
        'nft_profit',
        'chain_income',
        'reward_credit',
    ];

    public function __invoke(
        Request $request,
        UserLevelResolver $levelResolver,
        WalletService $walletService,
        StakeRewardService $stakeRewardService
    ): View {
        $user = $request->user();

        $stakeRewardService->creditDueRewardsForUser($user, $walletService);
        $user->loadMissing(['profile', 'latestKycRequest']);

        $displayName = filled($user->profile?->username)
            ? $user->profile->username
            : $user->name;

        return view('dashboard', [
            'user' => $user,
            'displayName' => $displayName,
            'avatarUrl' => $user->profile?->photo_url,
            'level' => $levelResolver->resolve($user),
            'walletBalance' => (float) $walletService->getBalance($user),
            'reserveBalance' => (float) $user->reserves()
                ->where('status', 'confirmed')
                ->sum('amount'),
            'dailyIncome' => (float) $user->walletLedgers()
                ->whereIn('type', self::INCOME_LEDGER_TYPES)
                ->whereDate('created_at', today())
                ->sum('amount'),
            'totalIncome' => (float) $user->walletLedgers()
                ->whereIn('type', self::INCOME_LEDGER_TYPES)
                ->sum('amount'),
            'recentWalletLedgers' => $user->walletLedgers()
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
            'emailVerified' => !is_null($user->email_verified_at),
            'twoFactorEnabled' => $user->hasTwoFactorEnabled(),
            'kycStatus' => $user->latestKycRequest?->status,
        ]);
    }
}
