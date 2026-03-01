<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\LevelResolver;
use App\Services\UserReserveService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SellController extends Controller
{
    public function index(Request $request, UserReserveService $userReserveService, LevelResolver $levelResolver): View|RedirectResponse
    {
        $user = $request->user();
        $level = $levelResolver->getUserLevel($user);
        $reservedBalance = $userReserveService->getBalance($user);

        if (!$level || $reservedBalance < (float) $level->min_reservation) {
            return redirect()->route('reserve.index')->withErrors([
                'amount' => 'Sell is locked. Reserve within your level to unlock.',
            ]);
        }

        return view('sell.index', [
            'level' => $level,
            'reservedBalance' => $reservedBalance,
        ]);
    }

    public function store(Request $request, UserReserveService $userReserveService, LevelResolver $levelResolver, WalletService $walletService): RedirectResponse
    {
        $data = $request->validate([
            'sale_amount' => ['required', 'numeric', 'min:0.00000001'],
        ]);

        $user = $request->user();
        $level = $levelResolver->getUserLevel($user);
        $reservedBalance = $userReserveService->getBalance($user);

        if (!$level || $reservedBalance < (float) $level->min_reservation) {
            return back()->withErrors(['sale_amount' => 'Sell is locked. Reserve more to unlock.']);
        }

        $saleAmount = (float) $data['sale_amount'];
        $min = (float) $level->income_min_percent;
        $max = (float) $level->income_max_percent;

        $percent = $min;
        if ($max > $min) {
            $percent = random_int((int) round($min * 1000), (int) round($max * 1000)) / 1000;
        }

        $income = ($saleAmount * $percent) / 100;

        DB::transaction(function () use ($user, $walletService, $saleAmount, $percent, $income, $level) {
            $walletService->credit($user, 'sell_income', $income, [
                'sale_amount' => $saleAmount,
                'percent' => $percent,
                'level_id' => $level->id,
            ]);
        });

        return back()->with('status', 'Sell completed. Income credited to wallet.');
    }
}
