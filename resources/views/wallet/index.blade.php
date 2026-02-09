@extends('layouts.app')

@section('content')
    <h1>Wallet</h1>

    <div class="mb-4">
        <strong>Balance:</strong> {{ number_format($balance, 4) }} USDT
    </div>

    <h2>Staking Plans</h2>
    @if ($plans->isEmpty())
        <p class="muted">No active plans.</p>
    @else
        <form method="POST" action="{{ route('staking.store') }}">
            @csrf
            <label for="plan_id">Plan</label>
            <select id="plan_id" name="plan_id" required>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}">
                        {{ $plan->name }} (Daily {{ $plan->daily_rate }}, Duration {{ $plan->duration_days }} days, Max x{{ $plan->max_payout_multiplier }})
                    </option>
                @endforeach
            </select>
            <label for="amount">Amount</label>
            <input id="amount" name="amount" type="number" step="0.0001" required>
            @error('amount') <div class="error">{{ $message }}</div> @enderror
            <button type="submit">Stake</button>
        </form>
    @endif

    <h2 style="margin-top:24px;">Your Stakes</h2>
    @if ($stakes->isEmpty())
        <p class="muted">No stakes yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>Principal</th>
                    <th>Status</th>
                    <th>Ends At</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stakes as $stake)
                    <tr>
                        <td>{{ $stake->stakePlan->name ?? '-' }}</td>
                        <td>{{ $stake->principal_amount }}</td>
                        <td>{{ ucfirst($stake->status) }}</td>
                        <td>{{ $stake->ends_at }}</td>
                        <td>
                            @if ($stake->status === 'active' && now()->gte($stake->ends_at))
                                <form method="POST" action="{{ route('staking.unstake', $stake) }}">
                                    @csrf
                                    <button type="submit">Unstake</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2 style="margin-top:24px;">Withdrawals</h2>
    <form method="POST" action="{{ route('withdrawals.store') }}">
        @csrf
        <label for="withdraw_amount">Amount</label>
        <input id="withdraw_amount" name="amount" type="number" step="0.0001" required>
        @error('amount') <div class="error">{{ $message }}</div> @enderror
        <button type="submit">Request Withdrawal</button>
    </form>

    @if ($withdrawals->isEmpty())
        <p class="muted">No withdrawals yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Eligible At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($withdrawals as $withdrawal)
                    <tr>
                        <td>{{ $withdrawal->amount }}</td>
                        <td>{{ ucfirst($withdrawal->status) }}</td>
                        <td>{{ $withdrawal->requested_at }}</td>
                        <td>{{ $withdrawal->eligible_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
