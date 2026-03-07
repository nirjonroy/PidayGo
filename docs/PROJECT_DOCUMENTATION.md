# PidayGo Project Documentation

This document is written for client handoff. It explains the frontend (home/public site), user panel, and admin panel, plus setup, configuration, and maintenance.

**Overview**
PidayGo is a Laravel 9 application with a public frontend, a user panel, and an admin panel. Feature flags and role/permission controls can enable or hide modules.

**URLs**
Public:
- `/` Home
- `/blog` Blog list
- `/blog/{slug}` Blog post
- `/explore` Explore (NFTs)
- `/rankings` Rankings
- `/login` Login
- `/register/{ref?}` Register (referral code required)

User panel:
- `/dashboard` Dashboard
- `/account/profile` Profile
- `/account/bank` Bank accounts
- `/notifications` Notifications
- `/support` Support
- `/wallet` Wallet
- `/wallet/deposit` Wallet deposit
- `/reserve` Reserve
- `/reserve/sell` Sell (Reserve)
- `/stake` Stake
- `/kyc` KYC

Admin:
- `/admin/login` Admin login
- `/admin` Admin dashboard
- `/admin/profile` Admin profile

**Registration Flow**
1. User registers with sponsor referral code (`ref_code`).
2. Email verification (if mail settings are active).
3. 2FA setup (Google Authenticator) if enabled.
4. KYC submission.
5. KYC approval is required before wallet, reserve, and stake features.

**Public Frontend**
Sections on the home page:
- Hero section: headline/subtitle from Site Settings, CTA buttons for Reserve and Stake, slider managed in Home Slides.
- Staking plans: shows available plans.
- Reservations summary: active reserves, today reserves, total reserved, and a recent activity table.
- Top sellers: seller list managed by admin.
- Latest news: blog posts managed by admin.

Blog:
- Admin creates blog posts with title, slug, category, image, excerpt, and publish date.

**User Panel**
Core modules:
- Profile: user details, user ID (`user_code`), referral code, notification preferences, profile image/banner.
- Bank accounts: add/edit/delete and set default.
- Wallet: balance overview, deposits, withdrawals.
- Reserve: reserve overview, confirm reserve actions, reserve sell.
- Stake: view plans, stake, and unstake.
- Notifications: read/dismiss notifications.
- Support: create ticket, message with admin, close ticket.

**Admin Panel**
Key areas:
- Site Settings: branding, hero text, footer content, theme colors, default theme mode, feature flags, deposit settings, USDT address.
- Home Slides: manage hero slider items.
- Blog Posts: create and publish blog content.
- Footer Links: manage footer menu items.
- Users: view user list and details.
- Roles and Permissions: manage admin access.
- Admins: manage admin accounts.
- Staking Plans and Levels: manage staking configuration.
- Reserve Plans and Reserve Ledger: manage reserve settings and balances.
- Deposits: review, approve, reject, expire.
- Withdrawals: review, approve, reject.
- Deposit Addresses: manage USDT deposit addresses.
- Sellers, NFTs, Bids: manage marketplace data.
- Notifications and Alerts: manage admin and user alerts.
- Support: review and respond to tickets.
- Chain Bonus: configure referral bonuses.
- Activity Logs: audit history.
- Mail Settings: configure SMTP and test mail.

**Theme and Branding**
Default theme mode:
- Auto: follow user selection.
- Light: force light by default.
- Dark: force dark by default.

Theme colors:
- Primary color controls major UI surfaces and accents.
- Secondary color controls button gradients and highlights.

Logos:
- Upload separate logos for normal, light, and dark themes.

**Feature Flags**
Enable or disable modules in Site Settings:
- Sellers
- NFTs/Explore
- Bids
- Reserve
- 2FA

**Security and Access Control**
- Email verification (optional, based on mail settings).
- 2FA (Google Authenticator).
- KYC approval required for finance actions.
- Admin IP restriction via `ADMIN_ALLOWED_IPS`.
- Role and permission based admin access.

**Tech Stack**
- Laravel 9.x
- PHP 8.x
- MySQL/MariaDB
- Laravel Breeze (auth)
- Spatie Permission
- Google2FA
- Simple QR Code

**Setup**
1. Install dependencies:
```bash
composer install
```
2. Environment:
```bash
cp .env.example .env
php artisan key:generate
```
3. Database:
```bash
php artisan migrate
php artisan db:seed
```
4. Storage:
```bash
php artisan storage:link
```

**Environment Configuration**
Update in `.env`:
- `APP_URL`
- `DB_*`
- `MAIL_*`
- `ADMIN_ALLOWED_IPS`

**Production Caching**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Backup and Maintenance**
- Database backups daily.
- Backup `storage/app/public` for uploaded files.
- Admin has a Clear Cache action.

**Troubleshooting**
- Theme not applying: clear browser cookies (`c_mod`) or clear cache from admin.
- Admin blocked: verify `ADMIN_ALLOWED_IPS`.
- Email not sending: check Mail Settings and `.env`.

**Deliverables**
This document covers:
- Frontend behavior
- User panel features
- Admin management tools
- Configuration and deployment notes

If you need a PDF or a shorter client quick guide, let me know.
