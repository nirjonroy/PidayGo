<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DepositAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DepositAddressController extends Controller
{
    public function index(): View
    {
        return view('admin.deposit-addresses.index', [
            'addresses' => DepositAddress::orderByDesc('updated_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.deposit-addresses.form', [
            'address' => new DepositAddress(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $address = DepositAddress::create($validated);
        ActivityLog::record('deposit.address.created', $request->user('admin'), $address);

        return redirect()->route('admin.deposit-addresses.index')->with('status', 'Deposit address created.');
    }

    public function edit(DepositAddress $depositAddress): View
    {
        return view('admin.deposit-addresses.form', [
            'address' => $depositAddress,
        ]);
    }

    public function update(Request $request, DepositAddress $depositAddress): RedirectResponse
    {
        $validated = $this->validatePayload($request, $depositAddress->id);

        $depositAddress->update($validated);
        ActivityLog::record('deposit.address.updated', $request->user('admin'), $depositAddress);

        return redirect()->route('admin.deposit-addresses.index')->with('status', 'Deposit address updated.');
    }

    public function activate(Request $request, DepositAddress $depositAddress): RedirectResponse
    {
        DB::transaction(function () use ($depositAddress) {
            DepositAddress::query()->update(['is_active' => false]);
            $depositAddress->update(['is_active' => true]);
        });

        ActivityLog::record('deposit.address.activated', $request->user('admin'), $depositAddress);

        return back()->with('status', 'Deposit address activated.');
    }

    public function deactivate(Request $request, DepositAddress $depositAddress): RedirectResponse
    {
        if ($depositAddress->is_active) {
            $depositAddress->update(['is_active' => false]);
            ActivityLog::record('deposit.address.deactivated', $request->user('admin'), $depositAddress);
        }

        return back()->with('status', 'Deposit address deactivated.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:150'],
            'currency' => ['required', 'string', 'max:10'],
            'chain' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:200', 'unique:deposit_addresses,address,' . ($ignoreId ?? 'NULL') . ',id'],
            'qr_payload' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
