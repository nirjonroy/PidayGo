<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\KycReviewController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReserveController as AdminReserveController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\DepositController as AdminDepositController;
use App\Http\Controllers\Admin\DepositAddressController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminAlertController;
use App\Http\Controllers\Admin\AdminConversationController;
use App\Http\Controllers\Admin\AdminMessageController;
use App\Http\Controllers\Admin\MailSettingsController;
use App\Http\Controllers\Admin\HomeSlideController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StakingPlanController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\WithdrawalReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\NftItemController;
use App\Http\Controllers\Admin\BidController;
use App\Http\Controllers\Admin\ReservePlanController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\User\DepositController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\User\StakeController;
use App\Http\Controllers\User\StakingController as UserStakingController;
use App\Http\Controllers\User\ReserveController as UserReserveController;
use App\Http\Controllers\User\UserConversationController;
use App\Http\Controllers\User\UserMessageController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\BankAccountController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\WithdrawalController as UserWithdrawalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return app(FrontendController::class)->home();
})->name('home');

Route::get('/explore', [FrontendController::class, 'explore'])->middleware('feature:nft_enabled')->name('explore');
Route::get('/item/{slug}', [FrontendController::class, 'itemDetails'])->middleware('feature:nft_enabled')->name('item.details');
Route::get('/rankings', function () {
    return view('frontend.rankings');
})->name('rankings');

Route::middleware('guest')->group(function () {
    Route::get('/register/{ref?}', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/email/verify', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])->name('password.confirm.store');

    Route::get('/two-factor/setup', [TwoFactorController::class, 'showSetup'])->name('two-factor.setup');
    Route::post('/two-factor/setup', [TwoFactorController::class, 'storeSetup'])->name('two-factor.store');
    Route::get('/two-factor/challenge', [TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge');
    Route::post('/two-factor/challenge', [TwoFactorController::class, 'verifyChallenge'])->name('two-factor.verify');
});

Route::middleware(['auth', 'verified.if.mail', '2fa.enabled', '2fa.passed'])->group(function () {
    Route::get('/kyc', [KycController::class, 'create'])->name('kyc.form');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.submit');
    Route::get('/kyc/status', [KycController::class, 'status'])->name('kyc.status');
});

Route::middleware(['auth', 'verified.if.mail', '2fa.enabled', '2fa.passed'])->group(function () {
    Route::get('/profile', function () {
        return redirect()->route('profile.edit');
    })->name('profile');

    Route::get('/account/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/account/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('/account/notifications', [UserProfileController::class, 'updateNotifications'])->name('profile.notifications.update');

    Route::get('/account/bank', [BankAccountController::class, 'index'])->name('profile.bank.index');
    Route::get('/account/bank/create', [BankAccountController::class, 'create'])->name('profile.bank.create');
    Route::post('/account/bank', [BankAccountController::class, 'store'])->name('profile.bank.store');
    Route::get('/account/bank/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('profile.bank.edit');
    Route::put('/account/bank/{bankAccount}', [BankAccountController::class, 'update'])->name('profile.bank.update');
    Route::delete('/account/bank/{bankAccount}', [BankAccountController::class, 'destroy'])->name('profile.bank.delete');
    Route::post('/account/bank/{bankAccount}/default', [BankAccountController::class, 'setDefault'])->name('profile.bank.default');

    Route::get('/notifications', [UserNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [UserNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/{notification}/dismiss', [UserNotificationController::class, 'dismiss'])->name('notifications.dismiss');
    Route::post('/notifications/{notification}/shown', [UserNotificationController::class, 'shown'])->name('notifications.shown');

    Route::get('/support', [UserConversationController::class, 'index'])->name('support.index');
    Route::get('/support/create', [UserConversationController::class, 'create'])->name('support.create');
    Route::post('/support', [UserConversationController::class, 'store'])->name('support.store');
    Route::get('/support/{conversation}', [UserConversationController::class, 'show'])->name('support.show');
    Route::post('/support/{conversation}/message', [UserMessageController::class, 'store'])->name('support.message.store');
    Route::post('/support/{conversation}/close', [UserConversationController::class, 'close'])->name('support.close');
});

Route::middleware(['auth', 'verified.if.mail', '2fa.enabled', '2fa.passed', 'kyc.approved'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/deposit', [DepositController::class, 'create'])->name('wallet.deposit');
    Route::post('/wallet/deposit', [DepositController::class, 'store'])->name('wallet.deposit.store');
    Route::get('/reserve', [UserReserveController::class, 'index'])->name('reserve.index');
    Route::post('/reserve/confirm', [UserReserveController::class, 'confirm'])->name('reserve.confirm');
    Route::get('/reserve/sell', [UserReserveController::class, 'sellForm'])->name('reserve.sell.form');
    Route::post('/reserve/sell', [UserReserveController::class, 'sellSubmit'])->name('reserve.sell.submit');
    Route::get('/sell', fn () => redirect()->route('reserve.sell.form'))->name('sell.index');
    Route::post('/staking', [UserStakingController::class, 'store'])->name('staking.store');
    Route::post('/staking/{stake}/unstake', [UserStakingController::class, 'unstake'])->name('staking.unstake');
    Route::post('/withdrawals', [UserWithdrawalController::class, 'store'])->name('withdrawals.store');
});

Route::middleware(['auth', 'verified.if.mail', 'ensure.user2fa', 'ensure.kyc'])->group(function () {
    Route::get('/stake', [StakeController::class, 'index'])->name('stake.index');
    Route::post('/stake', [StakeController::class, 'store'])->name('stake.store');
});

Route::prefix('admin')->name('admin.')->middleware('admin.ip')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::middleware('permission:kyc.review')->group(function () {
            Route::get('/kyc', [KycReviewController::class, 'index'])->name('kyc.index');
            Route::get('/kyc/{kycRequest}', [KycReviewController::class, 'show'])->name('kyc.show');
            Route::get('/kyc/{kycRequest}/file/{type}', [KycReviewController::class, 'file'])->name('kyc.file');
            Route::post('/kyc/{kycRequest}/approve', [KycReviewController::class, 'approve'])->name('kyc.approve');
            Route::post('/kyc/{kycRequest}/reject', [KycReviewController::class, 'reject'])->name('kyc.reject');
        });

        Route::middleware('permission:site.manage')->group(function () {
            Route::get('/site-settings', [SiteSettingController::class, 'index'])->name('site-settings.index');
            Route::get('/site-settings/create', [SiteSettingController::class, 'create'])->name('site-settings.create');
            Route::post('/site-settings', [SiteSettingController::class, 'store'])->name('site-settings.store');
            Route::get('/site-settings/edit', [SiteSettingController::class, 'edit'])->name('site-settings.edit');
            Route::post('/site-settings/update', [SiteSettingController::class, 'update'])->name('site-settings.update');
        });

        Route::middleware('permission:home.slide.manage')->group(function () {
            Route::get('/home-slides', [HomeSlideController::class, 'index'])->name('home-slides.index');
            Route::get('/home-slides/create', [HomeSlideController::class, 'create'])->name('home-slides.create');
            Route::post('/home-slides', [HomeSlideController::class, 'store'])->name('home-slides.store');
            Route::get('/home-slides/{homeSlide}/edit', [HomeSlideController::class, 'edit'])->name('home-slides.edit');
            Route::post('/home-slides/{homeSlide}/update', [HomeSlideController::class, 'update'])->name('home-slides.update');
            Route::post('/home-slides/{homeSlide}/toggle', [HomeSlideController::class, 'toggle'])->name('home-slides.toggle');
            Route::post('/home-slides/{homeSlide}/delete', [HomeSlideController::class, 'destroy'])->name('home-slides.delete');
        });

        Route::middleware('permission:role.manage')->group(function () {
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::post('/roles/{role}/update', [RoleController::class, 'update'])->name('roles.update');
            Route::post('/roles/{role}/delete', [RoleController::class, 'destroy'])->name('roles.delete');
        });

        Route::middleware('permission:permission.manage')->group(function () {
            Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
            Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
            Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
            Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
            Route::post('/permissions/{permission}/update', [PermissionController::class, 'update'])->name('permissions.update');
            Route::post('/permissions/{permission}/delete', [PermissionController::class, 'destroy'])->name('permissions.delete');
        });

        Route::middleware('permission:user.manage')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        });

        Route::middleware('permission:admin.manage')->group(function () {
            Route::get('/admins', [AdminAccountController::class, 'index'])->name('admins.index');
            Route::get('/admins/create', [AdminAccountController::class, 'create'])->name('admins.create');
            Route::post('/admins', [AdminAccountController::class, 'store'])->name('admins.store');
            Route::get('/admins/{admin}/edit', [AdminAccountController::class, 'edit'])->name('admins.edit');
            Route::post('/admins/{admin}/update', [AdminAccountController::class, 'update'])->name('admins.update');
            Route::post('/admins/{admin}/delete', [AdminAccountController::class, 'destroy'])->name('admins.delete');
        });

        Route::middleware('permission:activity.view')->group(function () {
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.index');
        });

        Route::middleware('permission:staking.manage')->group(function () {
            Route::get('/staking-plans', [StakingPlanController::class, 'index'])->name('staking-plans.index');
            Route::get('/staking-plans/create', [StakingPlanController::class, 'create'])->name('staking-plans.create');
            Route::post('/staking-plans', [StakingPlanController::class, 'store'])->name('staking-plans.store');
            Route::get('/staking-plans/{stakingPlan}/edit', [StakingPlanController::class, 'edit'])->name('staking-plans.edit');
            Route::post('/staking-plans/{stakingPlan}/update', [StakingPlanController::class, 'update'])->name('staking-plans.update');
            Route::post('/staking-plans/{stakingPlan}/delete', [StakingPlanController::class, 'destroy'])->name('staking-plans.delete');
        });

        Route::middleware('permission:level.manage')->group(function () {
            Route::get('/levels', [LevelController::class, 'index'])->name('levels.index');
            Route::get('/levels/create', [LevelController::class, 'create'])->name('levels.create');
            Route::post('/levels', [LevelController::class, 'store'])->name('levels.store');
            Route::get('/levels/{level}/edit', [LevelController::class, 'edit'])->name('levels.edit');
            Route::post('/levels/{level}/update', [LevelController::class, 'update'])->name('levels.update');
            Route::post('/levels/{level}/toggle', [LevelController::class, 'toggle'])->name('levels.toggle');
        });

        Route::middleware('permission:withdrawal.review')->group(function () {
            Route::get('/withdrawals', [WithdrawalReviewController::class, 'index'])->name('withdrawals.index');
            Route::post('/withdrawals/{withdrawal}/approve', [WithdrawalReviewController::class, 'approve'])->name('withdrawals.approve');
            Route::post('/withdrawals/{withdrawal}/reject', [WithdrawalReviewController::class, 'reject'])->name('withdrawals.reject');
        });

        Route::middleware('permission:deposit.review')->group(function () {
            Route::get('/deposits', [AdminDepositController::class, 'index'])->name('deposits.index');
            Route::get('/deposits/{deposit}', [AdminDepositController::class, 'show'])->name('deposits.show');
            Route::post('/deposits/{deposit}/approve', [AdminDepositController::class, 'approve'])->name('deposits.approve');
            Route::post('/deposits/{deposit}/reject', [AdminDepositController::class, 'reject'])->name('deposits.reject');
            Route::post('/deposits/{deposit}/expire', [AdminDepositController::class, 'expire'])->name('deposits.expire');
        });

        Route::middleware('permission:deposit.address.manage')->group(function () {
            Route::get('/deposit-addresses', [DepositAddressController::class, 'index'])->name('deposit-addresses.index');
            Route::get('/deposit-addresses/create', [DepositAddressController::class, 'create'])->name('deposit-addresses.create');
            Route::post('/deposit-addresses', [DepositAddressController::class, 'store'])->name('deposit-addresses.store');
            Route::get('/deposit-addresses/{depositAddress}/edit', [DepositAddressController::class, 'edit'])->name('deposit-addresses.edit');
            Route::put('/deposit-addresses/{depositAddress}', [DepositAddressController::class, 'update'])->name('deposit-addresses.update');
            Route::post('/deposit-addresses/{depositAddress}/activate', [DepositAddressController::class, 'activate'])->name('deposit-addresses.activate');
            Route::post('/deposit-addresses/{depositAddress}/deactivate', [DepositAddressController::class, 'deactivate'])->name('deposit-addresses.deactivate');
        });

        Route::middleware('permission:reserve.manage')->group(function () {
            Route::get('/reserve', [AdminReserveController::class, 'index'])->name('reserve.index');
            Route::post('/reserve/add', [AdminReserveController::class, 'add'])->name('reserve.add');
            Route::post('/reserve/deduct', [AdminReserveController::class, 'deduct'])->name('reserve.deduct');
            Route::get('/reserve/ledger', [AdminReserveController::class, 'ledger'])->name('reserve.ledger');

            Route::get('/reserve-plans', [ReservePlanController::class, 'index'])->name('reserve-plans.index');
            Route::get('/reserve-plans/create', [ReservePlanController::class, 'create'])->name('reserve-plans.create');
            Route::post('/reserve-plans', [ReservePlanController::class, 'store'])->name('reserve-plans.store');
            Route::get('/reserve-plans/{reservePlan}/edit', [ReservePlanController::class, 'edit'])->name('reserve-plans.edit');
            Route::post('/reserve-plans/{reservePlan}/update', [ReservePlanController::class, 'update'])->name('reserve-plans.update');
            Route::post('/reserve-plans/{reservePlan}/toggle', [ReservePlanController::class, 'toggle'])->name('reserve-plans.toggle');
            Route::post('/reserve-plans/{reservePlan}/delete', [ReservePlanController::class, 'destroy'])->name('reserve-plans.delete');
        });

        Route::middleware('permission:notification.manage')->group(function () {
            Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
            Route::get('/notifications/create', [AdminNotificationController::class, 'create'])->name('notifications.create');
            Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
        });

        Route::middleware('permission:seller.manage')->group(function () {
            Route::get('/sellers', [SellerController::class, 'index'])->name('sellers.index');
            Route::get('/sellers/create', [SellerController::class, 'create'])->name('sellers.create');
            Route::post('/sellers', [SellerController::class, 'store'])->name('sellers.store');
            Route::get('/sellers/{seller}/edit', [SellerController::class, 'edit'])->name('sellers.edit');
            Route::post('/sellers/{seller}/update', [SellerController::class, 'update'])->name('sellers.update');
            Route::post('/sellers/{seller}/toggle', [SellerController::class, 'toggle'])->name('sellers.toggle');
            Route::post('/sellers/{seller}/delete', [SellerController::class, 'destroy'])->name('sellers.delete');
        });

        Route::middleware('permission:nft.manage')->group(function () {
            Route::get('/nft-items', [NftItemController::class, 'index'])->name('nft-items.index');
            Route::get('/nft-items/create', [NftItemController::class, 'create'])->name('nft-items.create');
            Route::post('/nft-items', [NftItemController::class, 'store'])->name('nft-items.store');
            Route::get('/nft-items/{nftItem}/edit', [NftItemController::class, 'edit'])->name('nft-items.edit');
            Route::post('/nft-items/{nftItem}/update', [NftItemController::class, 'update'])->name('nft-items.update');
            Route::post('/nft-items/{nftItem}/toggle', [NftItemController::class, 'toggle'])->name('nft-items.toggle');
            Route::post('/nft-items/{nftItem}/delete', [NftItemController::class, 'destroy'])->name('nft-items.delete');
        });

        Route::middleware('permission:bid.manage')->group(function () {
            Route::get('/bids', [BidController::class, 'index'])->name('bids.index');
            Route::get('/bids/create', [BidController::class, 'create'])->name('bids.create');
            Route::post('/bids', [BidController::class, 'store'])->name('bids.store');
            Route::post('/bids/{bid}/toggle', [BidController::class, 'toggle'])->name('bids.toggle');
            Route::post('/bids/{bid}/delete', [BidController::class, 'destroy'])->name('bids.delete');
        });

        Route::get('/alerts', [AdminAlertController::class, 'index'])->name('alerts.index');
        Route::post('/alerts/{notification}/read', [AdminAlertController::class, 'markRead'])->name('alerts.read');
        Route::post('/alerts/{notification}/dismiss', [AdminAlertController::class, 'dismiss'])->name('alerts.dismiss');

        Route::middleware('permission:support.manage')->group(function () {
            Route::get('/support', [AdminConversationController::class, 'index'])->name('support.index');
            Route::get('/support/{conversation}', [AdminConversationController::class, 'show'])->name('support.show');
            Route::post('/support/{conversation}/message', [AdminMessageController::class, 'store'])->name('support.message.store');
            Route::post('/support/{conversation}/assign', [AdminConversationController::class, 'assign'])->name('support.assign');
            Route::post('/support/{conversation}/status', [AdminConversationController::class, 'status'])->name('support.status');
        });

        Route::middleware('permission:mail.manage')->group(function () {
            Route::get('/mail-settings', [MailSettingsController::class, 'index'])->name('mail-settings.index');
            Route::post('/mail-settings', [MailSettingsController::class, 'update'])->name('mail-settings.update');
            Route::post('/mail-settings/test', [MailSettingsController::class, 'test'])->name('mail-settings.test');
        });

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
});
