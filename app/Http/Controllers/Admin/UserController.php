<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\UserReserveLedger;
use App\Models\WalletLedger;
use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('latestKycRequest');

        if ($request->filled('user_code')) {
            $query->where('user_code', $request->string('user_code'));
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->string('email') . '%');
        }

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('user_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kyc_status')) {
            $status = $request->string('kyc_status');
            if ($status === 'none') {
                $query->whereDoesntHave('latestKycRequest');
            } else {
                $query->whereHas('latestKycRequest', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            }
        }

        if ($request->filled('date_from')) {
            $from = Carbon::parse($request->string('date_from'))->startOfDay();
            $query->where('created_at', '>=', $from);
        }

        if ($request->filled('date_to')) {
            $to = Carbon::parse($request->string('date_to'))->endOfDay();
            $query->where('created_at', '<=', $to);
        }

        $users = $query->orderByDesc('created_at')->paginate(20)->appends($request->query());

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $request->only(['user_code', 'email', 'q', 'kyc_status', 'date_from', 'date_to']),
        ]);
    }

    public function show(User $user): View
    {
        $user->load([
            'profile',
            'bankAccounts',
            'latestKycRequest',
            'stakes.stakePlan',
        ]);

        $walletBalance = $user->walletLedgers()->sum('amount');
        $reserveBalance = $user->reserve?->reserved_balance ?? 0;
        $walletLedgers = WalletLedger::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(30)
            ->get();
        $reserveLedgers = UserReserveLedger::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();
        $depositRequests = DepositRequest::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(30)
            ->get();
        $withdrawals = WithdrawalRequest::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        return view('admin.users.show', [
            'user' => $user,
            'walletBalance' => $walletBalance,
            'reserveBalance' => $reserveBalance,
            'walletLedgers' => $walletLedgers,
            'reserveLedgers' => $reserveLedgers,
            'depositRequests' => $depositRequests,
            'withdrawals' => $withdrawals,
        ]);
    }
}
