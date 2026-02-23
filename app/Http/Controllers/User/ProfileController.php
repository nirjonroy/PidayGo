<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'profile' => $request->user()->profile ?? new UserProfile(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        $validated = $request->validate([
            'username' => ['nullable', 'string', 'max:50', Rule::unique('user_profiles', 'username')->ignore($profile?->id)],
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
        ]);

        $photoPath = $profile?->photo_path;
        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('avatars', 'public');
        }

        $data = $validated;
        unset($data['photo']);
        $data['photo_path'] = $photoPath;

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return back()->with('status', 'Profile updated.');
    }
}
