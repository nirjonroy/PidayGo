@extends('layouts.frontend')

@section('content')
<!-- section begin -->
            <section id="subheader" class="jarallax text-light">
                <img class="jarallax-img" src="{{ asset('frontend/images/background/subheader.jpg') }}" alt="" />
                <div class="center-y relative text-center">
                    <div class="container">
                        <div class="row">

                            <div class="col-md-12 text-center">
                                <h1>Explore</h1>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- section close -->


            <!-- section begin -->
            <section aria-label="section">
                <div class="container">
                    <div class="row wow fadeIn">
                        <div class="col-lg-12">

                            <div class="items_filter">
                                <form action="blank.php" class="row form-dark" id="form_quick_search" method="post" name="form_quick_search">
                                    <div class="col text-center">
                                        <input class="form-control" id="name_1" name="name_1" placeholder="search item here..." type="text" /> <a href="#" id="btn-submit"><i class="fa fa-search bg-color-secondary"></i></a>
                                        <div class="clearfix"></div>
                                    </div>
                                </form>

                                <div id="item_category" class="dropdown">
                                    <a href="#" class="btn-selector">All categories</a>
                                    <ul>
                                        <li class="active"><span>All categories</span></li>
                                        <li><span>Art</span></li>
                                        <li><span>Music</span></li>
                                        <li><span>Domain Names</span></li>
                                        <li><span>Virtual World</span></li>
                                        <li><span>Trading Cards</span></li>
                                        <li><span>Collectibles</span></li>
                                        <li><span>Sports</span></li>
                                        <li><span>Utility</span></li>
                                    </ul>
                                </div>

                                <div id="buy_category" class="dropdown">
                                    <a href="#" class="btn-selector">Buy Now</a>
                                    <ul>
                                        <li class="active"><span>Buy Now</span></li>
                                        <li><span>On Auction</span></li>
                                        <li><span>Has Offers</span></li>
                                    </ul>
                                </div>

                                <div id="items_type" class="dropdown">
                                    <a href="#" class="btn-selector">All Items</a>
                                    <ul>
                                        <li class="active"><span>All Items</span></li>
                                        <li><span>Single Items</span></li>
                                        <li><span>Bundles</span></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        @forelse ($items as $item)
                        @php
                            $itemImage = \Illuminate\Support\Str::startsWith($item->image_path, 'frontend/')
                                ? asset($item->image_path)
                                : asset('storage/' . $item->image_path);
                        @endphp
                        <div class="d-item col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="nft__item">
                                @if ($item->auction_end_at)
                                    <div class="de_countdown" data-year="{{ $item->auction_end_at->format('Y') }}" data-month="{{ $item->auction_end_at->format('n') }}" data-day="{{ $item->auction_end_at->format('j') }}" data-hour="{{ $item->auction_end_at->format('G') }}"></div>
                                @endif
                                <div class="author_list_pp">
                                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Creator: {{ $item->creatorSeller?->name ?? 'Unknown' }}">
                                        <img class="lazy" src="{{ $item->creatorSeller && $item->creatorSeller->avatar_path ? (\Illuminate\Support\Str::startsWith($item->creatorSeller->avatar_path, 'frontend/') ? asset($item->creatorSeller->avatar_path) : asset('storage/' . $item->creatorSeller->avatar_path)) : asset('frontend/images/author/author-1.jpg') }}" alt="">
                                        @if ($item->creatorSeller?->is_verified)
                                            <i class="fa fa-check"></i>
                                        @endif
                                    </a>
                                </div>
                                <div class="nft__item_wrap">
                                    <div class="nft__item_extra">
                                        <div class="nft__item_buttons">
                                            <button onclick="location.href='{{ route('item.details', $item->slug) }}'">View</button>
                                            <div class="nft__item_share">
                                                <h4>Share</h4>
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url('/item/' . $item->slug) }}" target="_blank"><i class="fa fa-facebook fa-lg"></i></a>
                                                <a href="https://twitter.com/intent/tweet?url={{ url('/item/' . $item->slug) }}" target="_blank"><i class="fa fa-twitter fa-lg"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('item.details', $item->slug) }}">
                                        <div class="d-placeholder"></div>
                                        <img src="{{ $itemImage }}" class="nft__item_preview" alt="">
                                    </a>
                                </div>
                                <div class="nft__item_info">
                                    <a href="{{ route('item.details', $item->slug) }}">
                                        <h4>{{ $item->title }}</h4>
                                    </a>
                                    <div class="nft__item_click">
                                        <span></span>
                                    </div>
                                    <div class="nft__item_price">
                                        {{ $item->price ? number_format($item->price, 4) . ' USDT' : '?' }}<span>#{{ $item->id }}</span>
                                    </div>
                                    <div class="nft__item_action">
                                        <a href="{{ route('item.details', $item->slug) }}">View Details</a>
                                    </div>
                                    <div class="nft__item_like">
                                        <i class="fa fa-heart"></i><span>{{ $item->likes_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-md-12 text-center">
                            <p class="text-muted">No items found.</p>
                        </div>
                        @endforelse
                        <div class="col-md-12 text-center mt-3">
                            {{ $items->links('vendor.pagination.gigaland') }}
                        </div>
                    </div>
                </div>
            </section>

@endsection
