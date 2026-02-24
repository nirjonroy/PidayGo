@extends('layouts.frontend')

@section('content')
<section id="nft-item-details" aria-label="section" class="sm-mt-0">
                <div class="container">
                    <div class="row g-5">
                        <div class="col-md-6 text-center">
                            @php
                                $itemImage = \Illuminate\Support\Str::startsWith($item->image_path, 'frontend/')
                                    ? asset($item->image_path)
                                    : asset('storage/' . $item->image_path);
                            @endphp
                            <div class="nft-image-wrapper">
                                <img src="{{ $itemImage }}" class="image-autosize img-fluid img-rounded mb-sm-30" alt="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="item_info">
                                @if ($item->auction_end_at)
                                    Auctions ends in
                                    <div class="de_countdown"
                                        data-year="{{ $item->auction_end_at->format('Y') }}"
                                        data-month="{{ $item->auction_end_at->format('n') }}"
                                        data-day="{{ $item->auction_end_at->format('j') }}"
                                        data-hour="{{ $item->auction_end_at->format('G') }}"></div>
                                @else
                                    Fixed Price Item
                                @endif
                                <h2>{{ $item->title }}</h2>
                                <div class="item_info_counts">
                                    <div class="item_info_type"><i class="fa fa-image"></i>Art</div>
                                    <div class="item_info_views"><i class="fa fa-eye"></i>{{ $item->views_count }}</div>
                                    <div class="item_info_like"><i class="fa fa-heart"></i>{{ $item->likes_count }}</div>
                                </div>
                                <p>{{ $item->description ?? 'No description available.' }}</p>

                                <div class="d-flex flex-row">
                                    <div class="mr40">
                                        <h6>Creator</h6>
                                        <div class="item_author">
                                            <div class="author_list_pp">
                                                <a href="#">
                                                    <img class="lazy" src="{{ $item->creatorSeller && $item->creatorSeller->avatar_path ? (\Illuminate\Support\Str::startsWith($item->creatorSeller->avatar_path, 'frontend/') ? asset($item->creatorSeller->avatar_path) : asset('storage/' . $item->creatorSeller->avatar_path)) : asset('frontend/images/author/author-1.jpg') }}" alt="">
                                                    @if ($item->creatorSeller?->is_verified)
                                                        <i class="fa fa-check"></i>
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="author_list_info">
                                                <a href="#">{{ $item->creatorSeller?->name ?? 'Unknown' }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h6>Collection</h6>
                                        <div class="item_author">
                                            <div class="author_list_pp">
                                                <a href="#">
                                                    <img class="lazy" src="{{ $item->ownerSeller && $item->ownerSeller->avatar_path ? (\Illuminate\Support\Str::startsWith($item->ownerSeller->avatar_path, 'frontend/') ? asset($item->ownerSeller->avatar_path) : asset('storage/' . $item->ownerSeller->avatar_path)) : asset('frontend/images/author/author-10.jpg') }}" alt="">
                                                    @if ($item->ownerSeller?->is_verified)
                                                        <i class="fa fa-check"></i>
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="author_list_info">
                                                <a href="#">{{ $item->ownerSeller?->name ?? 'N/A' }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="spacer-40"></div>

                                <div class="de_tab tab_simple">

                                    <ul class="de_nav">
                                        <li class="active"><span>Details</span></li>
                                        @if (feature('bids_enabled'))
                                            <li><span>Bids</span></li>
                                        @endif
                                        <li><span>History</span></li>
                                    </ul>

                                    <div class="de_tab_content">
                                        <div class="tab-1">
                                            <h6>Owner</h6>
                                            <div class="item_author">
                                                <div class="author_list_pp">
                                                    <a href="#">
                                                        <img class="lazy" src="{{ $item->ownerSeller && $item->ownerSeller->avatar_path ? (\Illuminate\Support\Str::startsWith($item->ownerSeller->avatar_path, 'frontend/') ? asset($item->ownerSeller->avatar_path) : asset('storage/' . $item->ownerSeller->avatar_path)) : asset('frontend/images/author/author-10.jpg') }}" alt="">
                                                        @if ($item->ownerSeller?->is_verified)
                                                            <i class="fa fa-check"></i>
                                                        @endif
                                                    </a>
                                                </div>
                                                <div class="author_list_info">
                                                    <a href="#">{{ $item->ownerSeller?->name ?? 'N/A' }}</a>
                                                </div>
                                            </div>

                                            <div class="spacer-40"></div>
                                            <h6>Properties</h6>
                                            <div class="row gx-2">
                                                <div class="col-lg-4 col-md-6 col-sm-6">
                                                    <a href="#" class="nft_attr">
                                                        <h5>Background</h5>
                                                        <h4>Yellowish Sky</h4>
                                                        <span>85% have this trait</span>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6">
                                                    <a href="#" class="nft_attr">
                                                        <h5>Eyes</h5>
                                                        <h4>Purple Eyes</h4>
                                                        <span>14% have this trait</span>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6">
                                                    <a href="#" class="nft_attr">
                                                        <h5>Nose</h5>
                                                        <h4>Small Nose</h4>
                                                        <span>45% have this trait</span>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6">
                                                    <a href="#" class="nft_attr">
                                                        <h5>Mouth</h5>
                                                        <h4>Smile Red Lip</h4>
                                                        <span>61% have this trait</span>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6">
                                                    <a href="#" class="nft_attr">
                                                        <h5>Neck</h5>
                                                        <h4>Pink Ribbon</h4>
                                                        <span>27% have this trait</span>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6">
                                                    <a href="#" class="nft_attr">
                                                        <h5>Hair</h5>
                                                        <h4>Pink Short</h4>
                                                        <span>35% have this trait</span>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6">
                                                    <a href="#" class="nft_attr">
                                                        <h5>Accessories</h5>
                                                        <h4>Heart Necklace</h4>
                                                        <span>33% have this trait</span>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6">
                                                    <a href="#" class="nft_attr">
                                                        <h5>Hat</h5>
                                                        <h4>Cute Panda</h4>
                                                        <span>62% have this trait</span>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6">
                                                    <a href="#" class="nft_attr">
                                                        <h5>Clothes</h5>
                                                        <h4>Casual Purple</h4>
                                                        <span>78% have this trait</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="spacer-30"></div>
                                        </div>

                                        @if (feature('bids_enabled'))
                                            <div class="tab-2">
                                                @forelse ($item->bids as $bid)
                                                    <div class="p_list">
                                                        <div class="p_list_pp">
                                                            <a href="#">
                                                                <img class="lazy" src="{{ asset('frontend/images/author/author-1.jpg') }}" alt="">
                                                                <i class="fa fa-check"></i>
                                                            </a>
                                                        </div>
                                                        <div class="p_list_info">
                                                            Bid <b>{{ number_format($bid->amount, 4) }} USDT</b>
                                                            <span>
                                                                by <b>{{ $bid->user?->name ?? $bid->bidder_name ?? 'Anonymous' }}</b>
                                                                at {{ $bid->created_at->format('M d, Y H:i') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-muted">No bids yet.</div>
                                                @endforelse
                                            </div>
                                        @endif

                                        <div class="tab-3">
                                            <div class="text-muted">No history yet.</div>
                                        </div>

                                    </div>

                                    <div class="spacer-10"></div>

                                    <h6>Price</h6>
                                    <div class="nft-item-price">
                                        <img src="{{ asset('frontend/images/misc/ethereum.svg') }}" alt="">
                                        <span>{{ $item->price ? number_format($item->price, 4) : 'N/A' }}</span>
                                        @if ($item->price)
                                            (USDT)
                                        @endif
                                    </div>

                                    <!-- Button trigger modal -->
                                    <a href="#" class="btn-main btn-lg" data-bs-toggle="modal" data-bs-target="#buy_now">
                                  Buy Now
                                </a>
                                    @if (feature('bids_enabled'))
                                        &nbsp;
                                        <a href="#" class="btn-main btn-lg btn-light" data-bs-toggle="modal" data-bs-target="#place_a_bid">
                                      Place a Bid
                                    </a>
                                    @endif

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </section>


@endsection
