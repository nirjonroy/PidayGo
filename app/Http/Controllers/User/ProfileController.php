<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\ChainCommission;
use App\Models\User;
use App\Models\WalletLedger;
use App\Models\UserNotificationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $directA = $user->referrals()->where('chain_slot', 'A')->count();
        $directB = $user->referrals()->where('chain_slot', 'B')->count();
        $directC = $user->referrals()->where('chain_slot', 'C')->count();

        $userId = $user->id;
        $downlineCount = User::query()
            ->where(function ($query) use ($userId) {
                $query->where('chain_path', 'like', $userId . '/%')
                    ->orWhere('chain_path', 'like', '%/' . $userId . '/%');
            })
            ->count();

        $chainIncomeTotal = (float) WalletLedger::query()
            ->where('user_id', $user->id)
            ->where('type', 'chain_income')
            ->sum('amount');

        $recentChain = ChainCommission::with('sourceUser')
            ->where('target_user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('profile.edit', [
            'profile' => $user->profile ?? new UserProfile(),
            'user' => $user,
            'notificationSettings' => $request->user()->notificationSettings ?? new UserNotificationSetting([
                'system_alerts' => true,
                'item_sold' => true,
                'auction_expiration' => true,
                'bid_activity' => true,
                'outbid' => true,
                'price_change' => true,
                'successful_purchase' => true,
            ]),
            'directCounts' => ['A' => $directA, 'B' => $directB, 'C' => $directC],
            'downlineCount' => $downlineCount,
            'chainIncomeTotal' => $chainIncomeTotal,
            'recentChain' => $recentChain,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        $validated = $request->validate([
            'username' => ['nullable', 'string', 'max:50', Rule::unique('user_profiles', 'username')->ignore($profile?->id)],
            'custom_url' => ['nullable', 'string', 'max:80', Rule::unique('user_profiles', 'custom_url')->ignore($profile?->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:500'],
            'dob' => ['nullable', 'date'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'social_twitter' => ['nullable', 'string', 'max:100'],
            'social_telegram' => ['nullable', 'string', 'max:100'],
            'social_discord' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $photoPath = $profile?->photo_path;
        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('users', 'public');
        }

        $bannerPath = $profile?->banner_path;
        if ($request->hasFile('banner')) {
            if ($bannerPath && Storage::disk('public')->exists($bannerPath)) {
                Storage::disk('public')->delete($bannerPath);
            }
            $bannerPath = $request->file('banner')->store('users', 'public');
        }

        $data = $validated;
        unset($data['photo']);
        unset($data['banner']);
        $data['photo_path'] = $photoPath;
        $data['banner_path'] = $bannerPath;

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return back()->with('status', 'Profile updated.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'system_alerts' => ['nullable', 'boolean'],
            'item_sold' => ['nullable', 'boolean'],
            'auction_expiration' => ['nullable', 'boolean'],
            'bid_activity' => ['nullable', 'boolean'],
            'outbid' => ['nullable', 'boolean'],
            'price_change' => ['nullable', 'boolean'],
            'successful_purchase' => ['nullable', 'boolean'],
        ]);

        UserNotificationSetting::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'system_alerts' => $request->boolean('system_alerts'),
                'item_sold' => $request->boolean('item_sold'),
                'auction_expiration' => $request->boolean('auction_expiration'),
                'bid_activity' => $request->boolean('bid_activity'),
                'outbid' => $request->boolean('outbid'),
                'price_change' => $request->boolean('price_change'),
                'successful_purchase' => $request->boolean('successful_purchase'),
            ]
        );

        return back()->with('status', 'Notification preferences updated.');
    }
}
