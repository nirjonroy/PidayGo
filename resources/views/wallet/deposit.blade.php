@extends('layouts.app')

@section('content')
    <h1>USDT Deposit (TRC20)</h1>

    <div class="mb-4">
        <strong>Deposit Chain:</strong> {{ $currency }}-{{ $chain }}<br>
        <strong>Minimum Deposit:</strong> {{ number_format($minDeposit, 4) }} USDT<br>
        <strong>Review Time:</strong> Within {{ $reviewHours }} hours
    </div>

    @if (empty($address))
        <div class="error">Deposit is temporarily unavailable. Please contact support.</div>
    @else
        <div class="mb-3">
            <button type="button" id="btn-show-qr">Show QR</button>
            <button type="button" id="btn-show-address" style="margin-left:8px;">Show Address</button>
        </div>

        <div id="qr-box" style="margin-bottom:16px;">
            {!! QrCode::size(220)->margin(1)->generate($qrPayload ?? $address) !!}
        </div>

        <div id="address-box" style="display:none; margin-bottom:16px;">
            <div style="display:flex; gap:8px; align-items:center;">
                <input type="text" id="deposit-address" value="{{ $address }}" readonly>
                <button type="button" id="copy-address">Copy</button>
            </div>
            <div id="copy-toast" class="muted" style="display:none; margin-top:6px;">Address copied.</div>
        </div>

        <div class="mb-4">
            <strong>How to deposit:</strong>
            <ol>
                <li>Copy address</li>
                <li>Open Binance App</li>
                <li>Wallet → Withdraw/Send</li>
                <li>Select USDT</li>
                <li>Network: TRC20</li>
                <li>Paste address, enter amount, confirm</li>
                <li>Copy TxID and submit below</li>
            </ol>
        </div>

        <form method="POST" action="{{ route('wallet.deposit.store') }}">
            @csrf
            <label for="amount">Amount (USDT)</label>
            <input id="amount" name="amount" type="number" step="0.0001" min="{{ $minDeposit }}" required>
            @error('amount') <div class="error">{{ $message }}</div> @enderror

            <label for="txid">TxID</label>
            <input id="txid" name="txid" type="text" required>
            @error('txid') <div class="error">{{ $message }}</div> @enderror

            @error('address') <div class="error">{{ $message }}</div> @enderror

            <button type="submit">Submit Deposit</button>
        </form>
    @endif

    <h2 style="margin-top:24px;">Recent Deposits</h2>
    @if ($history->isEmpty())
        <p class="muted">No deposits yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>TxID</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($history as $item)
                    <tr>
                        <td>{{ $item->amount }}</td>
                        <td style="max-width:220px; word-break:break-all;">{{ $item->txid }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <script>
        const qrBox = document.getElementById('qr-box');
        const addressBox = document.getElementById('address-box');
        const btnShowQr = document.getElementById('btn-show-qr');
        const btnShowAddress = document.getElementById('btn-show-address');
        const copyBtn = document.getElementById('copy-address');
        const toast = document.getElementById('copy-toast');

        if (btnShowQr && btnShowAddress) {
            btnShowQr.addEventListener('click', () => {
                qrBox.style.display = 'block';
                addressBox.style.display = 'none';
            });
            btnShowAddress.addEventListener('click', () => {
                qrBox.style.display = 'none';
                addressBox.style.display = 'block';
            });
        }

        if (copyBtn) {
            copyBtn.addEventListener('click', async () => {
                const input = document.getElementById('deposit-address');
                try {
                    await navigator.clipboard.writeText(input.value);
                    toast.style.display = 'block';
                    setTimeout(() => { toast.style.display = 'none'; }, 1500);
                } catch (e) {
                    input.select();
                    document.execCommand('copy');
                    toast.style.display = 'block';
                    setTimeout(() => { toast.style.display = 'none'; }, 1500);
                }
            });
        }
    </script>
@endsection
