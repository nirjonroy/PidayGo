@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Profile'])

<section aria-label="section">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="nft__item s2 mb30">
                    <div class="nft__item_info">
                        <h4 class="mb-3">Profile Details</h4>
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control" value="{{ $user->email }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">User ID</label>
                                <input class="form-control" value="{{ $user->user_code }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Referral Code</label>
                                <div class="input-group">
                                    <input class="form-control" value="{{ $user->ref_code }}" id="ref-code" readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('ref-code').value)">Copy</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Profile Photo</label>
                                    <div class="mb-2">
                                        @if ($profile->photo_url)
                                            <img src="{{ $profile->photo_url }}" alt="Profile Photo" style="width:120px;height:120px;object-fit:cover;border-radius:8px;">
                                        @else
                                            <div class="text-muted">No photo uploaded.</div>
                                        @endif
                                    </div>
                                    <input type="file" name="photo" class="form-control">
                                    @error('photo')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Profile Banner</label>
                                    <div class="mb-2">
                                        @if ($profile->banner_url)
                                            <img src="{{ $profile->banner_url }}" alt="Profile Banner" style="width:100%;max-height:120px;object-fit:cover;border-radius:8px;">
                                        @else
                                            <div class="text-muted">No banner uploaded.</div>
                                        @endif
                                    </div>
                                    <input type="file" name="banner" class="form-control">
                                    @error('banner')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Username</label>
                                    <input name="username" class="form-control" value="{{ old('username', $profile->username) }}">
                                    @error('username')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Custom URL</label>
                                    <input name="custom_url" class="form-control" value="{{ old('custom_url', $profile->custom_url) }}" placeholder="your-name">
                                    @error('custom_url')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Phone</label>
                                    <input name="phone" class="form-control" value="{{ old('phone', $profile->phone) }}">
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Country</label>
                                    <input name="country" class="form-control" value="{{ old('country', $profile->country) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">City</label>
                                    <input name="city" class="form-control" value="{{ old('city', $profile->city) }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" value="{{ old('dob', optional($profile->dob)->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $profile->address) }}</textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="3">{{ old('bio', $profile->bio) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Twitter</label>
                                    <input name="social_twitter" class="form-control" value="{{ old('social_twitter', $profile->social_twitter) }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Telegram</label>
                                    <input name="social_telegram" class="form-control" value="{{ old('social_telegram', $profile->social_telegram) }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Discord</label>
                                    <input name="social_discord" class="form-control" value="{{ old('social_discord', $profile->social_discord) }}">
                                </div>
                            </div>

                            <div class="mt-3 d-flex gap-2">
                                <button class="btn-main" type="submit">Save Profile</button>
                                <a class="btn-main btn-light" href="{{ route('profile.bank.index') }}">Manage Bank Accounts</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="nft__item s2 mb30">
                    <div class="nft__item_info">
                        <h4 class="mb-3">Notification Preferences</h4>
                        <form method="POST" action="{{ route('profile.notifications.update') }}">
                            @csrf
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="system_alerts" id="system_alerts" value="1" @checked(old('system_alerts', $notificationSettings->system_alerts))>
                                <label class="form-check-label" for="system_alerts">System alerts (deposits, withdrawals, stakes, KYC)</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="item_sold" id="item_sold" value="1" @checked(old('item_sold', $notificationSettings->item_sold))>
                                <label class="form-check-label" for="item_sold">Item sold</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="auction_expiration" id="auction_expiration" value="1" @checked(old('auction_expiration', $notificationSettings->auction_expiration))>
                                <label class="form-check-label" for="auction_expiration">Auction expiration</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="bid_activity" id="bid_activity" value="1" @checked(old('bid_activity', $notificationSettings->bid_activity))>
                                <label class="form-check-label" for="bid_activity">Bid activity</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="outbid" id="outbid" value="1" @checked(old('outbid', $notificationSettings->outbid))>
                                <label class="form-check-label" for="outbid">Outbid</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="price_change" id="price_change" value="1" @checked(old('price_change', $notificationSettings->price_change))>
                                <label class="form-check-label" for="price_change">Price change</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="successful_purchase" id="successful_purchase" value="1" @checked(old('successful_purchase', $notificationSettings->successful_purchase))>
                                <label class="form-check-label" for="successful_purchase">Successful purchase</label>
                            </div>

                            <button class="btn-main btn-light" type="submit">Save Preferences</button>
                        </form>
                    </div>
                </div>

                <div class="nft__item s2 mb30">
                    <div class="nft__item_info">
                        <h4 class="mb-3">Chain Details</h4>
                        <div class="nft__item_price">Sponsor Code: {{ $user->sponsor?->ref_code ?? 'N/A' }}</div>
                        <div class="nft__item_price">My Chain Slot: {{ $user->chain_slot ?? '-' }}</div>
                        <div class="nft__item_price">Direct A: {{ $directCounts['A'] ?? 0 }}</div>
                        <div class="nft__item_price">Direct B: {{ $directCounts['B'] ?? 0 }}</div>
                        <div class="nft__item_price">Direct C: {{ $directCounts['C'] ?? 0 }}</div>
                        <div class="nft__item_price">Total Downline: {{ $downlineCount }}</div>
                        <div class="nft__item_price">Chain Income Total: {{ number_format($chainIncomeTotal, 8) }} USDT</div>
                    </div>
                </div>

                <div class="nft__item s2 mb30">
                    <div class="nft__item_info">
                        <h4 class="mb-3">Recent Chain Income</h4>
                        @if ($recentChain->isEmpty())
                            <div class="text-muted">No chain income yet.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>From</th>
                                            <th>Depth</th>
                                            <th>%</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentChain as $row)
                                            <tr>
                                                <td>{{ $row->sourceUser?->ref_code ?? $row->source_user_id }}</td>
                                                <td>{{ $row->level_depth }}</td>
                                                <td>{{ $row->percent }}%</td>
                                                <td>{{ number_format((float) $row->amount, 8) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
