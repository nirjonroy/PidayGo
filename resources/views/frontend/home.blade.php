@extends('layouts.frontend')

@section('content')
<section id="section-hero" class="no-top no-bottom mt90 sm-mt-0" aria-label="section">
                <div class="m-5 padding30 br-15 bg-custom hero-card">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6 mb-sm-30">
                                <h1>{{ $heroHeadline ?? 'Explore, collect, and sell extraordinary NFTs' }}</h1>
                                <p class="lead">
                                    {{ $heroSubtitle ?? 'Welcome to the future, you can buy and sell awesome artworks form here. The world largest digital marketplace for non-fungible tokens.' }}
                                </p>
                                <div class="hero-actions">
                                    <a href="{{ route('explore') }}" class="btn-main btn-lg">Explore</a>
                                    <a href="{{ auth()->check() ? route('stake.index') : route('login') }}" class="btn-main btn-lg btn-light">Stake</a>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="d-carousel">
                                    <div id="item-carousel-big-type-2" class="owl-carousel owl-center" data-wow-delay="1s">
                                        @if ($slides->isEmpty())
                                            <div class="nft_pic mod-b br-15">
                                                <a href="{{ route('explore') }}">
                                                    <span class="nft_pic_info">
                                                        <span class="nft_pic_title">Featured Collection</span>
                                                        <span class="nft_pic_by">PidayGo</span>
                                                    </span>
                                                    <div class="nft_pic_wrap">
                                                        <img src="{{ asset('frontend/images/carousel/crs-12.jpg') }}" class="lazy img-fluid" alt="">
                                                    </div>
                                                </a>
                                            </div>
                                        @else
                                            @foreach ($slides as $slide)
                                                @php
                                                    $slideImage = \Illuminate\Support\Str::startsWith($slide->image_path, 'slides/')
                                                        ? asset('storage/' . $slide->image_path)
                                                        : asset($slide->image_path);
                                                @endphp
                                                <div class="nft_pic mod-b br-15">
                                                    <a href="{{ $slide->button_url ?: route('explore') }}">
                                                        <span class="nft_pic_info">
                                                            <span class="nft_pic_title">{{ $slide->title }}</span>
                                                            @if ($slide->subtitle)
                                                                <span class="nft_pic_by">{{ $slide->subtitle }}</span>
                                                            @endif
                                                        </span>
                                                        <div class="nft_pic_wrap">
                                                            <img src="{{ $slideImage }}" class="lazy img-fluid" alt="">
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="d-arrow-left mod-a"><i class="fa fa-angle-left"></i></div>
                                    <div class="d-arrow-right mod-a"><i class="fa fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section aria-label="section" class="mt-20 no-top no-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Stake &amp; Earn</h2>
                            <p class="lead">Choose a staking plan and start earning daily rewards.</p>
                        </div>
                    </div>
                    <div class="row">
                        @forelse ($plans as $plan)
                            <div class="col-lg-4 col-md-6 mb30">
                                <div class="nft__item s2">
                                    <div class="nft__item_info">
                                        <h4>{{ $plan->name }}</h4>
                                        <div class="nft__item_price">
                                            Daily Rate: {{ $plan->daily_rate }}%
                                        </div>
                                        <div class="nft__item_price">
                                            Min: {{ $plan->min_amount ?? 0 }} / Max: {{ $plan->max_amount ?? 'Unlimited' }}
                                        </div>
                                        <div class="nft__item_price">
                                            Duration: {{ $plan->duration_days }} days
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route('stake.index') }}" class="btn-main">Start Staking</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-md-12">
                                <p class="text-muted">No staking plans available right now.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            @if (feature('sellers_enabled'))
            <section id="section-collections" class="no-top no-bottom">
                <div class="m-5 mt-0 mb-0 padding30 br-15 bg-custom">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="top-sellers-header">
                                    <h2 class="top-sellers-title">Top Sellers in</h2>
                                    <div id="top_sellers_filter" class="dropdown alt-2 top-sellers-filter">
                                    <a href="#" class="btn-selector">30 days</a>
                                    <ul>
                                        <li class="active"><span>30 days</span></li>
                                        <li><span>1 day</span></li>
                                        <li><span>7 days</span></li>
                                    </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 wow fadeIn">
                                <div class="row g-3 top-sellers-grid">
                                    @forelse ($topSellers as $seller)
                                        @php
                                            $avatar = $seller->avatar_path
                                                ? (\Illuminate\Support\Str::startsWith($seller->avatar_path, 'frontend/')
                                                    ? asset($seller->avatar_path)
                                                    : asset('storage/' . $seller->avatar_path))
                                                : asset('frontend/images/author/author-1.jpg');
                                        @endphp
                                        <div class="col-lg-4 col-md-6">
                                            <div class="seller-card">
                                                <div class="seller-avatar">
                                                    <img class="lazy" src="{{ $avatar }}" alt="">
                                                    @if ($seller->is_verified)
                                                        <span class="seller-badge"><i class="fa fa-check"></i></span>
                                                    @endif
                                                </div>
                                                <div class="seller-meta">
                                                    <div class="seller-name">{{ $seller->name }}</div>
                                                    <div class="seller-username">{{ '@' . $seller->username }}</div>
                                                </div>
                                                <div class="seller-volume">{{ number_format($seller->volume, 4) }} USDT</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-md-12">
                                            <div class="seller-empty">No sellers found.</div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            @if (feature('nft_enabled'))
            <section id="section-trending" class="pt40 no-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Trending NFTs</h2>
                            <div id="items-carousel-s2" class="owl-carousel wow fadeIn">
                                  @forelse ($trendingItems as $item)
                                      @php
                                          $itemImage = \Illuminate\Support\Str::startsWith($item->image_path, 'frontend/')
                                              ? asset($item->image_path)
                                              : asset('storage/' . $item->image_path);
                                      @endphp
                                      <div class="d-item">
                                          <div class="nft__item s2">
                                              @if ($item->auction_end_at)
                                                  <div class="de_countdown"
                                                      data-year="{{ $item->auction_end_at->format('Y') }}"
                                                      data-month="{{ $item->auction_end_at->format('n') }}"
                                                      data-day="{{ $item->auction_end_at->format('j') }}"
                                                      data-hour="{{ $item->auction_end_at->format('G') }}"></div>
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
                                                      <img src="{{ $itemImage }}" class="lazy nft__item_preview" alt="">
                                                  </a>
                                              </div>
                                              <div class="nft__item_info">
                                                  <a href="{{ route('item.details', $item->slug) }}">
                                                      <h4>{{ $item->title }}</h4>
                                                  </a>
                                                  <div class="nft__item_click"><span></span></div>
                                                  <div class="nft__item_price">
                                                      {{ $item->price ? number_format($item->price, 4) . ' USDT' : '?' }}<span>#{{ $item->id }}</span>
                                                  </div>
                                                  <div class="nft__item_like">
                                                      <i class="fa fa-heart"></i><span>{{ $item->likes_count }}</span>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  @empty
                                      <div class="text-muted">No trending items.</div>
                                  @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            @if (feature('bids_enabled'))
            <section id="section-bids" class="pt40 no-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Latest Bids</h2>
                        </div>
                        <div class="col-lg-12">
                            <div class="nft__item s2">
                                <div class="nft__item_info">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Bidder</th>
                                                    <th>Amount</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($latestBids as $bid)
                                                    <tr>
                                                        <td>{{ $bid->item?->title ?? '-' }}</td>
                                                        <td>{{ $bid->user?->name ?? $bid->bidder_name ?? 'Anonymous' }}</td>
                                                        <td>{{ number_format($bid->amount, 4) }} USDT</td>
                                                        <td>{{ $bid->created_at->diffForHumans() }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No bids yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            <section id="section-category" class="pt20 no-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Popular Categories</h2>
                        </div>
                        <div class="col-md-2 col-sm-4 col-12 mb-sm-30 wow fadeInRight" data-wow-delay=".1s">
                            <a href="{{ route('explore') }}" class="icon-box style-2 rounded">
                                <i class="fa fa-image"></i>
                                <span>Art</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-12 mb-sm-30 wow fadeInRight" data-wow-delay=".2s">
                            <a href="{{ route('explore') }}" class="icon-box style-2 rounded">
                                <i class="fa fa-music"></i>
                                <span>Music</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-12 mb-sm-30 wow fadeInRight" data-wow-delay=".3s">
                            <a href="{{ route('explore') }}" class="icon-box style-2 rounded">
                                <i class="fa fa-search"></i>
                                <span>Domain Names</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-12 mb-sm-30 wow fadeInRight" data-wow-delay=".4s">
                            <a href="{{ route('explore') }}" class="icon-box style-2 rounded">
                                <i class="fa fa-globe"></i>
                                <span>Virtual Worlds</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-12 mb-sm-30 wow fadeInRight" data-wow-delay=".5s">
                            <a href="{{ route('explore') }}" class="icon-box style-2 rounded">
                                <i class="fa fa-vcard"></i>
                                <span>Trading Cards</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-12 mb-sm-30 wow fadeInRight" data-wow-delay=".6s">
                            <a href="{{ route('explore') }}" class="icon-box style-2 rounded">
                                <i class="fa fa-th"></i>
                                <span>More Categories</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="section-news" class="pt40" data-bgimage="url({{ asset('frontend/images/background/23.jpg') }}) top" data-bgimage-alt="url({{ asset('frontend/images/background/23-alt.jpg') }}) top">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Latest News</h2>
                        </div>
                    </div>

                    <div class="row wow fadeIn">
                        <div class="col-lg-4 col-md-6 mb-sm-30">
                            <div class="bloglist item">
                                <div class="post-content">
                                    <div class="post-image">
                                        <img alt="" src="{{ asset('frontend/images/news/news-b1.jpg') }}" class="lazy">
                                    </div>
                                    <div class="post-text">
                                        <span class="p-tagline">Tips &amp; Tricks</span>
                                        <span class="p-date">October 28, 2020</span>
                                        <h4><a href="#">How to create NFT item<span></span></a></h4>
                                        <p>The NFT can be associated with a particular digital or physical asset such as images, art, music, and sport highlights and may confer licensing rights....</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-sm-30">
                            <div class="bloglist item">
                                <div class="post-content">
                                    <div class="post-image">
                                        <img alt="" src="{{ asset('frontend/images/news/news-b2.jpg') }}" class="lazy">
                                    </div>
                                    <div class="post-text">
                                        <span class="p-tagline">Tips &amp; Tricks</span>
                                        <span class="p-date">October 28, 2020</span>
                                        <h4><a href="#">How to sell NFT item<span></span></a></h4>
                                        <p>NFTs function like cryptographic tokens, but unlike cryptocurrencies such as Bitcoin or Ethereum, NFTs are not mutually interchangeable...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-sm-30">
                            <div class="bloglist item">
                                <div class="post-content">
                                    <div class="post-image">
                                        <img alt="" src="{{ asset('frontend/images/news/news-b3.jpg') }}" class="lazy">
                                    </div>
                                    <div class="post-text">
                                        <span class="p-tagline">Tips &amp; Tricks</span>
                                        <span class="p-date">October 28, 2020</span>
                                        <h4><a href="#">Where to sell NFT item<span></span></a></h4>
                                        <p>The ownership of an NFT as defined by the blockchain has no inherent legal meaning, and does not necessarily grant copyright, intellectual property...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

@endsection











