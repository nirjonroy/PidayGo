@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'USDT Deposit (TRC20)'])

<section aria-label="section">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <div><strong>Deposit Chain:</strong> {{ $currency }}-{{ $chain }}</div>
                        <div><strong>Minimum Deposit:</strong> {{ number_format($minDeposit, 4) }} USDT</div>
                        <div><strong>Review Time:</strong> Within {{ $reviewHours }} hours</div>
                    </div>
                </div>
            </div>
        </div>

        @if (empty($address))
            <div class="text-danger">Deposit is temporarily unavailable. Please contact support.</div>
        @else
            <div class="row">
                <div class="col-lg-6 mb30">
                    <div class="nft__item s2">
                        <div class="nft__item_info">
                            <div class="mb-3">
                                <button type="button" id="btn-show-qr" class="btn-main btn-light btn-sm">Show QR</button>
                                <button type="button" id="btn-show-address" class="btn-main btn-sm">Show Address</button>
                            </div>

                            <div id="qr-box" class="mb-3">
                                {!! QrCode::size(220)->margin(1)->generate($qrPayload ?? $address) !!}
                            </div>

                            <div id="address-box" style="display:none;" class="mb-3">
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="text" id="deposit-address" class="form-control" value="{{ $address }}" readonly>
                                    <button type="button" id="copy-address" class="btn-main btn-light btn-sm">Copy</button>
                                </div>
                                <div id="copy-toast" class="text-muted mt-2" style="display:none;">Address copied.</div>
                            </div>

                            <div>
                                <strong>How to deposit:</strong>
                                <ol class="mt-2">
                                    <li>Copy address</li>
                                    <li>Open Binance App</li>
                                    <li>Wallet -> Withdraw/Send</li>
                                    <li>Select USDT</li>
                                    <li>Network: TRC20</li>
                                    <li>Paste address, enter amount, confirm</li>
                                    <li>Copy TxID and submit below</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb30">
                    <form method="POST" action="{{ route('wallet.deposit.store') }}" class="form-border">
                        @csrf
                        <div class="field-set">
                            <h5>Submit Deposit</h5>
                            <input id="amount" name="amount" type="number" step="0.0001" min="{{ $minDeposit }}" class="form-control" placeholder="Amount (USDT)" required>
                            @error('amount') <div class="text-danger">{{ $message }}</div> @enderror

                            <div class="spacer-20"></div>

                            <input id="txid" name="txid" type="text" class="form-control" placeholder="TxID" required>
                            @error('txid') <div class="text-danger">{{ $message }}</div> @enderror

                            @error('address') <div class="text-danger">{{ $message }}</div> @enderror

                            <div class="spacer-20"></div>

                            <button type="submit" class="btn-main">Submit Deposit</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <h2>Recent Deposits</h2>
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>TxID</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($history as $item)
                                    <tr>
                                        <td>{{ number_format($item->amount, 4) }}</td>
                                        <td style="max-width:220px; word-break:break-all;">{{ $item->txid }}</td>
                                        <td>{{ ucfirst($item->status) }}</td>
                                        <td>{{ $item->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No deposits yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

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
