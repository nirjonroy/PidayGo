<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserBankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(Request $request): View
    {
        return view('profile.bank.index', [
            'accounts' => $request->user()->bankAccounts()->orderByDesc('is_default')->orderByDesc('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('profile.bank.form', [
            'account' => new UserBankAccount(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayload($request);
        $data['user_id'] = $request->user()->id;

        DB::transaction(function () use ($data, $request) {
            if (!empty($data['is_default'])) {
                UserBankAccount::where('user_id', $request->user()->id)->update(['is_default' => false]);
            }

            UserBankAccount::create($data);
        });

        return redirect()->route('profile.bank.index')->with('status', 'Bank account added.');
    }

    public function edit(Request $request, UserBankAccount $bankAccount): View
    {
        $this->authorizeOwnership($request, $bankAccount);

        return view('profile.bank.form', [
            'account' => $bankAccount,
        ]);
    }

    public function update(Request $request, UserBankAccount $bankAccount): RedirectResponse
    {
        $this->authorizeOwnership($request, $bankAccount);

        $data = $this->validatePayload($request);

        DB::transaction(function () use ($data, $bankAccount, $request) {
            if (!empty($data['is_default'])) {
                UserBankAccount::where('user_id', $request->user()->id)->update(['is_default' => false]);
            }

            $bankAccount->update($data);
        });

        return redirect()->route('profile.bank.index')->with('status', 'Bank account updated.');
    }

    public function destroy(Request $request, UserBankAccount $bankAccount): RedirectResponse
    {
        $this->authorizeOwnership($request, $bankAccount);
        $bankAccount->delete();

        return redirect()->route('profile.bank.index')->with('status', 'Bank account removed.');
    }

    public function setDefault(Request $request, UserBankAccount $bankAccount): RedirectResponse
    {
        $this->authorizeOwnership($request, $bankAccount);

        DB::transaction(function () use ($request, $bankAccount) {
            UserBankAccount::where('user_id', $request->user()->id)->update(['is_default' => false]);
            $bankAccount->update(['is_default' => true]);
        });

        return redirect()->route('profile.bank.index')->with('status', 'Default bank account updated.');
    }

    private function authorizeOwnership(Request $request, UserBankAccount $bankAccount): void
    {
        if ($bankAccount->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function validatePayload(Request $request): array
    {
        $rules = [
            'network' => ['required', 'string', 'max:30'],
            'wallet_address' => ['required', 'string', 'max:255'],
            'address_label' => ['nullable', 'string', 'max:120'],
            'memo_tag' => ['nullable', 'string', 'max:120'],
            'is_default' => ['nullable', 'boolean'],
        ];

        $validated = $request->validate($rules);
        $validated['is_default'] = (bool) ($validated['is_default'] ?? false);

        return $validated;
    }
}
