<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\StakePlan;
use App\Models\Stake;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $balance = (float) $user->walletLedgers()->sum('amount');

        return view('wallet.index', [
            'balance' => $balance,
            'plans' => StakePlan::where('is_active', true)->get(),
            'stakes' => Stake::where('user_id', $user->id)->latest()->get(),
            'withdrawals' => WithdrawalRequest::where('user_id', $user->id)->latest()->get(),
        ]);
    }
}
