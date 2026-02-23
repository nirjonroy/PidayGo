<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\StakePlan;
use App\Models\Stake;
use App\Models\WithdrawalRequest;
use App\Services\UserReserveService;
use App\Services\LevelResolver;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request, UserReserveService $userReserveService, LevelResolver $levelResolver)
    {
        $user = $request->user();
        $balance = (float) $user->walletLedgers()->sum('amount');
        $reservedBalance = $userReserveService->getBalance($user);
        $todayEarnings = (float) $user->walletLedgers()
            ->where('type', 'reward_credit')
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');
        $cumulativeIncome = (float) $user->walletLedgers()
            ->where('type', 'reward_credit')
            ->sum('amount');
        $level = $levelResolver->getUserLevel($user);

        return view('wallet.index', [
            'balance' => $balance,
            'reservedBalance' => $reservedBalance,
            'todayEarnings' => $todayEarnings,
            'cumulativeIncome' => $cumulativeIncome,
            'level' => $level,
            'plans' => StakePlan::where('is_active', true)->get(),
            'stakes' => Stake::where('user_id', $user->id)->latest()->get(),
            'withdrawals' => WithdrawalRequest::where('user_id', $user->id)->latest()->get(),
        ]);
    }
}
