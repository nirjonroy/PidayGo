@extends('layouts.app')

@section('content')
    <h1>Stake</h1>

    <div class="mb-4">
        <strong>Available Balance:</strong> {{ number_format((float) $balance, 4) }} USDT
    </div>

    <h2>Active Plans</h2>
    @if ($plans->isEmpty())
        <p class="muted">No active plans right now.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Daily Rate</th>
                    <th>Duration (days)</th>
                    <th>Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($plans as $plan)
                    <tr>
                        <td>{{ $plan->name }}</td>
                        <td>{{ $plan->min_amount ?? '-' }}</td>
                        <td>{{ $plan->max_amount ?? '-' }}</td>
                        <td>{{ $plan->daily_rate }}</td>
                        <td>{{ $plan->duration_days }}</td>
                        <td>{{ $plan->level_required ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form id="stake-form" method="POST" action="{{ route('stake.store') }}" style="margin-top: 18px;">
            @csrf
            <label for="stake_plan_id">Plan</label>
            <select id="stake_plan_id" name="stake_plan_id" required>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}">
                        {{ $plan->name }} (Min {{ $plan->min_amount ?? 0 }}, Max {{ $plan->max_amount ?? '∞' }}, Daily {{ $plan->daily_rate }}, {{ $plan->duration_days }} days)
                    </option>
                @endforeach
            </select>
            @error('stake_plan_id') <div class="error">{{ $message }}</div> @enderror

            <label for="amount">Amount</label>
            <input id="amount" name="amount" type="number" step="0.0001" min="0.0001" required value="{{ old('amount') }}">
            @error('amount') <div class="error">{{ $message }}</div> @enderror

            <button type="submit" id="stake-submit">Create Stake</button>
        </form>
    @endif

    <h2 style="margin-top:24px;">Your Active Stakes</h2>
    @if ($stakes->isEmpty())
        <p class="muted">No active stakes yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>Principal</th>
                    <th>Daily Rate</th>
                    <th>Started</th>
                    <th>Total Reward Paid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stakes as $stake)
                    <tr>
                        <td>{{ $stake->stakePlan->name ?? '-' }}</td>
                        <td>{{ $stake->principal_amount }}</td>
                        <td>{{ $stake->stakePlan->daily_rate ?? '-' }}</td>
                        <td>{{ $stake->started_at }}</td>
                        <td>{{ $stake->total_reward_paid }}</td>
                        <td>{{ ucfirst($stake->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <script>
        if (window.jQuery) {
            $('#stake-form').on('submit', function () {
                $('#stake-submit').prop('disabled', true).text('Processing...');
            });
        }
    </script>
@endsection
