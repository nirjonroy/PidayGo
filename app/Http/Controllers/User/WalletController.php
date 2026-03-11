<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stake;
use App\Models\StakePlan;
use App\Services\UserReserveService;
use App\Services\UserLevelResolver;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request, UserReserveService $userReserveService, UserLevelResolver $levelResolver)
    {
        $user = $request->user();
        $balance = (float) $user->walletLedgers()->sum('amount');
        $reservedBalance = (float) $user->reserves()->where('status', 'confirmed')->sum('amount');
        $todayEarnings = (float) $user->walletLedgers()
            ->whereIn('type', ['nft_profit', 'chain_income'])
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');
        $cumulativeIncome = (float) $user->walletLedgers()
            ->whereIn('type', ['nft_profit', 'chain_income'])
            ->sum('amount');
        $recentWalletLedgers = $user->walletLedgers()
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
        $level = $levelResolver->resolve($user);
        $canSell = $reservedBalance > 0;

        return view('wallet.index', [
            'balance' => $balance,
            'reservedBalance' => $reservedBalance,
            'todayEarnings' => $todayEarnings,
            'cumulativeIncome' => $cumulativeIncome,
            'recentWalletLedgers' => $recentWalletLedgers,
            'level' => $level,
            'canSell' => $canSell,
            'plans' => StakePlan::where('is_active', true)->get(),
            'stakes' => Stake::where('user_id', $user->id)->latest()->get(),
        ]);
    }
}
