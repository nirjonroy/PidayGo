<!doctype html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Admin - {{ $siteName ?? 'PidayGo' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="{{ asset('adminlte/css/adminlte.css') }}" as="style" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media='all'"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.css') }}" />
    <style>
      .admin-popup-backdrop {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(17, 24, 39, 0.54);
        backdrop-filter: blur(6px);
        z-index: 2000;
      }
      .admin-popup-card {
        width: min(100%, 520px);
        border-radius: 24px;
        border: 0;
        background: #ffffff;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.28);
        overflow: hidden;
      }
      .admin-popup-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 22px 14px;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
      }
      .admin-popup-kicker {
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
      }
      .admin-popup-title {
        margin: 0;
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
      }
      .admin-popup-close {
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 12px;
        background: rgba(15, 23, 42, 0.06);
        color: #334155;
      }
      .admin-popup-body {
        padding: 20px 22px 12px;
      }
      .admin-popup-alert {
        border-radius: 18px;
        padding: 16px 18px;
        margin-bottom: 16px;
      }
      .admin-popup-alert h4 {
        margin: 0 0 8px;
        font-size: 20px;
        font-weight: 800;
      }
      .admin-popup-alert p {
        margin: 0;
        font-size: 15px;
        color: #334155;
      }
      .admin-popup-alert.is-info {
        background: rgba(59, 130, 246, 0.12);
      }
      .admin-popup-alert.is-success {
        background: rgba(16, 185, 129, 0.14);
      }
      .admin-popup-alert.is-warning {
        background: rgba(245, 158, 11, 0.16);
      }
      .admin-popup-alert.is-error {
        background: rgba(239, 68, 68, 0.14);
      }
      .admin-popup-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        color: #64748b;
        font-size: 14px;
      }
      .admin-popup-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        padding: 0 22px 22px;
      }
      .admin-popup-actions form,
      .admin-popup-actions a,
      .admin-popup-actions button {
        flex: 1 1 140px;
      }
      .admin-popup-actions .btn {
        width: 100%;
        border-radius: 14px;
        padding: 11px 16px;
        font-weight: 700;
      }
      @media (max-width: 575.98px) {
        .admin-popup-backdrop {
          padding: 14px;
        }
        .admin-popup-card {
          border-radius: 20px;
        }
        .admin-popup-head,
        .admin-popup-body,
        .admin-popup-actions {
          padding-left: 18px;
          padding-right: 18px;
        }
        .admin-popup-title {
          font-size: 22px;
        }
      }
    </style>
    @stack('styles')
  </head>
  <body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
      <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="{{ route('admin.dashboard') }}" class="nav-link">Home</a></li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                @if (!empty($adminNotificationCount))
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $adminNotificationCount }}
                  </span>
                @endif
              </a>
              <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:320px;">
                <div class="dropdown-header">Notifications</div>
                @forelse ($adminNotificationsPreview as $item)
                  <a class="dropdown-item" href="{{ route('admin.alerts.index') }}">
                    <div class="fw-semibold">{{ $item->notification->title }}</div>
                    <div class="small text-muted">{{ \Illuminate\Support\Str::limit($item->notification->message, 60) }}</div>
                  </a>
                @empty
                  <div class="dropdown-item text-muted">No notifications</div>
                @endforelse
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-center" href="{{ route('admin.alerts.index') }}">View all</a>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
              </a>
            </li>
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img
                  src="{{ asset('adminlte/assets/img/user2-160x160.jpg') }}"
                  class="user-image rounded-circle shadow"
                  alt="User Image"
                />
                <span class="d-none d-md-inline">{{ auth('admin')->user()->name ?? 'Admin' }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <li class="user-header text-bg-primary">
                  <img
                    src="{{ asset('adminlte/assets/img/user2-160x160.jpg') }}"
                    class="rounded-circle shadow"
                    alt="User Image"
                  />
                  <p>
                    {{ auth('admin')->user()->name ?? 'Admin' }}
                    <small>{{ auth('admin')->user()->email ?? '' }}</small>
                  </p>
                </li>
                <li class="user-footer">
                  <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-default btn-flat float-end">Sign out</button>
                  </form>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>

      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
          <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <img
              src="{{ !empty($siteLogo) ? asset('storage/' . $siteLogo) : asset('adminlte/assets/img/AdminLTELogo.png') }}"
              alt="Logo"
              class="brand-image opacity-75 shadow"
            />
            <span class="brand-text fw-light">{{ $siteName ?? 'PidayGo' }}</span>
          </a>
        </div>
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" data-accordion="false">
              <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.kyc.index') }}" class="nav-link {{ request()->routeIs('admin.kyc.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-person-check"></i>
                  <p>KYC Requests</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.site-settings.index') }}" class="nav-link {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-gear"></i>
                  <p>Site Settings</p>
                </a>
              </li>
              @if (auth('admin')->user()?->can('home.slide.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.home-slides.index') }}" class="nav-link {{ request()->routeIs('admin.home-slides.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-images"></i>
                    <p>Home Slides</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('blog.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.blog-posts.index') }}" class="nav-link {{ request()->routeIs('admin.blog-posts.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-journal-text"></i>
                    <p>Blog Posts</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('footer.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.footer-links.index') }}" class="nav-link {{ request()->routeIs('admin.footer-links.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-link-45deg"></i>
                    <p>Footer Links</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('user.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-people"></i>
                    <p>Users</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('role.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-shield-lock"></i>
                    <p>Roles</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('permission.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-key"></i>
                    <p>Permissions</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('admin.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.admins.index') }}" class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-people-fill"></i>
                    <p>Admins</p>
                  </a>
                </li>
              @endif
                @if (auth('admin')->user()?->can('staking.manage'))
                  <li class="nav-item">
                    <a href="{{ route('admin.staking-plans.index') }}" class="nav-link {{ request()->routeIs('admin.staking-plans.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-bank"></i>
                      <p>Staking Plans</p>
                    </a>
                  </li>
                @endif
                @if (auth('admin')->user()?->can('level.manage'))
                  <li class="nav-item">
                    <a href="{{ route('admin.levels.index') }}" class="nav-link {{ request()->routeIs('admin.levels.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-bar-chart-steps"></i>
                      <p>Levels</p>
                    </a>
                  </li>
                @endif
                @if (auth('admin')->user()?->can('reserve.manage'))
                  <li class="nav-item">
                    <a href="{{ route('admin.reserve-plans.index') }}" class="nav-link {{ request()->routeIs('admin.reserve-plans.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-diagram-3"></i>
                      <p>Reserve Plans</p>
                    </a>
                  </li>
                @endif
              @if (auth('admin')->user()?->can('withdrawal.review'))
                <li class="nav-item">
                  <a href="{{ route('admin.withdrawals.index') }}" class="nav-link {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-cash-coin"></i>
                    <p>Withdrawals</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('deposit.review'))
                <li class="nav-item">
                  <a href="{{ route('admin.deposits.index') }}" class="nav-link {{ request()->routeIs('admin.deposits.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-box-arrow-in-down"></i>
                    <p>Deposits</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('deposit.address.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.deposit-addresses.index') }}" class="nav-link {{ request()->routeIs('admin.deposit-addresses.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-geo"></i>
                    <p>Deposit Addresses</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('seller.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.sellers.index') }}" class="nav-link {{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-person-badge"></i>
                    <p>Sellers</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('nft.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.nft-items.index') }}" class="nav-link {{ request()->routeIs('admin.nft-items.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-grid"></i>
                    <p>NFT Items</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('bid.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.bids.index') }}" class="nav-link {{ request()->routeIs('admin.bids.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-cash-stack"></i>
                    <p>Bids</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('notification.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-bell"></i>
                    <p>Notifications</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('support.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.support.index') }}" class="nav-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-chat-dots"></i>
                    <p>Support</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('mail.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.mail-settings.index') }}" class="nav-link {{ request()->routeIs('admin.mail-settings.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-envelope-at"></i>
                    <p>Mail Settings</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('reserve.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.reserve.index') }}" class="nav-link {{ request()->routeIs('admin.reserve.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-safe2"></i>
                    <p>Reserve</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('chain.manage'))
                <li class="nav-item">
                  <a href="{{ route('admin.chain-bonuses.index') }}" class="nav-link {{ request()->routeIs('admin.chain-bonuses.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-diagram-2"></i>
                    <p>Chain Bonus</p>
                  </a>
                </li>
              @endif
              @if (auth('admin')->user()?->can('activity.view'))
                <li class="nav-item">
                  <a href="{{ route('admin.activity.index') }}" class="nav-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-clipboard-data"></i>
                    <p>Activity Logs</p>
                  </a>
                </li>
              @endif
              <li class="nav-item">
                <a href="{{ route('admin.profile.edit') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-person-gear"></i>
                  <p>Profile</p>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </aside>

      <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6">
                <h3 class="mb-0">@yield('page-title', 'Dashboard')</h3>
              </div>
              <div class="col-sm-6">
                @yield('breadcrumbs')
              </div>
            </div>
          </div>
        </div>
        <div class="app-content">
          <div class="container-fluid">
            @if (session('status'))
              <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @yield('content')
          </div>
        </div>
      </main>

      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">{{ ($siteName ?? 'PidayGo') }} Admin</div>
        <strong>
          Copyright &copy; 2024-{{ now()->year }}
          <span class="text-decoration-none">{{ $siteName ?? 'PidayGo' }}</span>.
        </strong>
        All rights reserved.
      </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = { scrollbarTheme: 'os-theme-light', scrollbarAutoHide: 'leave', scrollbarClickScroll: true };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: { theme: Default.scrollbarTheme, autoHide: Default.scrollbarAutoHide, clickScroll: Default.scrollbarClickScroll },
          });
        }

        const adminPopup = document.getElementById('admin-notif-modal');
        if (adminPopup) {
          adminPopup.style.display = 'flex';
          adminPopup.addEventListener('click', function (event) {
            if (event.target === adminPopup) {
              adminPopup.style.display = 'none';
            }
          });
        }

        document.querySelectorAll('[data-admin-popup-close]').forEach(function (button) {
          button.addEventListener('click', function () {
            const modal = document.getElementById('admin-notif-modal');
            if (modal) {
              modal.style.display = 'none';
            }
          });
        });
      });
    </script>

    @if (!empty($adminPopupNotification) && $adminPopupNotification->notification)
      @php
        $adminPopupLevel = $adminPopupNotification->notification->level ?? 'info';
        $adminPopupClass = match ($adminPopupLevel) {
          'success' => 'is-success',
          'warning' => 'is-warning',
          'error' => 'is-error',
          default => 'is-info',
        };
      @endphp
      <div class="admin-popup-backdrop" id="admin-notif-modal">
        <div class="admin-popup-card">
          <div class="admin-popup-head">
            <div>
              <p class="admin-popup-kicker">Unread Notification</p>
              <h3 class="admin-popup-title">Admin Alert</h3>
            </div>
            <button type="button" class="admin-popup-close" data-admin-popup-close aria-label="Close notification popup">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
          <div class="admin-popup-body">
            <div class="admin-popup-alert {{ $adminPopupClass }}">
              <h4>{{ $adminPopupNotification->notification->title }}</h4>
              <p>{{ $adminPopupNotification->notification->message }}</p>
            </div>
            <div class="admin-popup-meta">
              <span>{{ optional($adminPopupNotification->created_at)->format('M d, Y h:i A') }}</span>
              <span>{{ ucfirst($adminPopupLevel) }}</span>
            </div>
          </div>
          <div class="admin-popup-actions">
            <form method="POST" action="{{ route('admin.alerts.dismiss', $adminPopupNotification->notification) }}">
              @csrf
              <button type="submit" class="btn btn-outline-secondary">Dismiss</button>
            </form>
            <form method="POST" action="{{ route('admin.alerts.read', $adminPopupNotification->notification) }}">
              @csrf
              <button type="submit" class="btn btn-primary">Mark Read</button>
            </form>
            <a href="{{ route('admin.alerts.index') }}" class="btn btn-light border">View All</a>
          </div>
        </div>
      </div>
    @endif

    @stack('scripts')
  </body>
</html>
