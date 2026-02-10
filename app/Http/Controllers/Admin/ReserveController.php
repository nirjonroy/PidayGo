<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ReserveAccount;
use App\Models\ReserveLedger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReserveController extends Controller
{
    public function index(): View
    {
        $reserve = ReserveAccount::firstOrCreate(
            ['currency' => 'USDT'],
            ['balance' => 0]
        );

        return view('admin.reserve.index', [
            'reserve' => $reserve,
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.0001'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $admin = $request->user('admin');
        $amount = (float) $validated['amount'];

        DB::transaction(function () use ($admin, $amount, $validated) {
            $reserve = ReserveAccount::where('currency', 'USDT')->lockForUpdate()->first();

            if (!$reserve) {
                $reserve = ReserveAccount::create([
                    'currency' => 'USDT',
                    'balance' => 0,
                ]);
            }

            ReserveLedger::create([
                'reserve_account_id' => $reserve->id,
                'amount' => $amount,
                'reason' => $validated['reason'],
                'created_by_admin_id' => $admin->id,
                'meta' => ['action' => 'add'],
            ]);

            $reserve->balance = round((float) $reserve->balance + $amount, 8);
            $reserve->save();
        });

        $this->logActivity($admin, 'reserve.added', [
            'amount' => $amount,
            'reason' => $validated['reason'],
        ]);

        return back()->with('status', 'Reserve updated.');
    }

    public function deduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.0001'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $admin = $request->user('admin');
        $amount = (float) $validated['amount'];
        $error = null;

        DB::transaction(function () use ($admin, $amount, $validated, &$error) {
            $reserve = ReserveAccount::where('currency', 'USDT')->lockForUpdate()->first();

            if (!$reserve) {
                $error = 'Reserve account not initialized.';
                return;
            }

            $balance = (float) $reserve->balance;
            if ($balance < $amount) {
                $error = 'Insufficient reserve balance.';
                return;
            }

            ReserveLedger::create([
                'reserve_account_id' => $reserve->id,
                'amount' => -$amount,
                'reason' => $validated['reason'],
                'created_by_admin_id' => $admin->id,
                'meta' => ['action' => 'deduct'],
            ]);

            $reserve->balance = round($balance - $amount, 8);
            $reserve->save();
        });

        if ($error) {
            return back()->withErrors(['amount' => $error]);
        }

        $this->logActivity($admin, 'reserve.deducted', [
            'amount' => $amount,
            'reason' => $validated['reason'],
        ]);

        return back()->with('status', 'Reserve updated.');
    }

    public function ledger(): View
    {
        $ledgers = ReserveLedger::query()
            ->with('createdByAdmin')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.reserve.ledger', [
            'ledgers' => $ledgers,
        ]);
    }

    private function logActivity($admin, string $event, array $properties = []): void
    {
        if (function_exists('activity')) {
            activity()
                ->causedBy($admin)
                ->withProperties($properties)
                ->log($event);
            return;
        }

        ActivityLog::record($event, $admin, null, $properties);
    }
}
