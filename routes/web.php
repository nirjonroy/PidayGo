<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\KycReviewController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReserveController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StakingPlanController;
use App\Http\Controllers\Admin\WithdrawalReviewController;
use App\Http\Controllers\Admin\UserController;
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
use App\Http\Controllers\User\StakeController;
use App\Http\Controllers\User\StakingController as UserStakingController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\WithdrawalController as UserWithdrawalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
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

Route::middleware(['auth', 'verified', '2fa.enabled', '2fa.passed'])->group(function () {
    Route::get('/kyc', [KycController::class, 'create'])->name('kyc.form');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.submit');
    Route::get('/kyc/status', [KycController::class, 'status'])->name('kyc.status');
});

Route::middleware(['auth', 'verified', '2fa.enabled', '2fa.passed', 'kyc.approved'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/staking', [UserStakingController::class, 'store'])->name('staking.store');
    Route::post('/staking/{stake}/unstake', [UserStakingController::class, 'unstake'])->name('staking.unstake');
    Route::post('/withdrawals', [UserWithdrawalController::class, 'store'])->name('withdrawals.store');
});

Route::middleware(['auth', 'verified', 'ensure.user2fa', 'ensure.kyc'])->group(function () {
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

        Route::middleware('permission:withdrawal.review')->group(function () {
            Route::get('/withdrawals', [WithdrawalReviewController::class, 'index'])->name('withdrawals.index');
            Route::post('/withdrawals/{withdrawal}/approve', [WithdrawalReviewController::class, 'approve'])->name('withdrawals.approve');
            Route::post('/withdrawals/{withdrawal}/reject', [WithdrawalReviewController::class, 'reject'])->name('withdrawals.reject');
        });

        Route::middleware('permission:reserve.manage')->group(function () {
            Route::get('/reserve', [ReserveController::class, 'index'])->name('reserve.index');
            Route::post('/reserve/add', [ReserveController::class, 'add'])->name('reserve.add');
            Route::post('/reserve/deduct', [ReserveController::class, 'deduct'])->name('reserve.deduct');
            Route::get('/reserve/ledger', [ReserveController::class, 'ledger'])->name('reserve.ledger');
        });

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
});
