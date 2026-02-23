<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ReserveAccount;
use App\Models\ReserveLedger;
use App\Services\ReserveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReserveController extends Controller
{
    public function index(ReserveService $reserveService): View
    {
        $reserve = ReserveAccount::firstOrCreate(
            ['currency' => 'USDT'],
            ['balance' => 0]
        );

        return view('admin.reserve.index', [
            'reserve' => $reserve,
        ]);
    }

    public function add(Request $request, ReserveService $reserveService): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.0001'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $admin = $request->user('admin');
        $amount = (float) $validated['amount'];

        $reserveService->credit($amount, $validated['reason'], 'admin_add', null, $admin->id);

        $this->logActivity($admin, 'reserve.added', [
            'amount' => $amount,
            'reason' => $validated['reason'],
        ]);

        return back()->with('status', 'Reserve updated.');
    }

    public function deduct(Request $request, ReserveService $reserveService): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.0001'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $admin = $request->user('admin');
        $amount = (float) $validated['amount'];
        try {
            $reserveService->debit($amount, $validated['reason'], 'admin_deduct', null, $admin->id);
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['amount' => $exception->getMessage()]);
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
