<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function index(): View
    {
        return view('admin.sellers.index', [
            'sellers' => Seller::orderByDesc('updated_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.sellers.form', [
            'seller' => new Seller(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_verified'] = (bool) ($validated['is_verified'] ?? false);

        $seller = new Seller($validated);

        if ($request->hasFile('avatar')) {
            $seller->avatar_path = $request->file('avatar')->store('sellers', 'public');
        }

        $seller->save();
        ActivityLog::record('seller.created', $request->user('admin'), $seller);

        return redirect()->route('admin.sellers.index')->with('status', 'Seller created.');
    }

    public function edit(Seller $seller): View
    {
        return view('admin.sellers.form', [
            'seller' => $seller,
        ]);
    }

    public function update(Request $request, Seller $seller): RedirectResponse
    {
        $validated = $this->validatePayload($request, $seller->id);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_verified'] = (bool) ($validated['is_verified'] ?? false);

        $seller->fill($validated);

        if ($request->hasFile('avatar')) {
            if ($seller->avatar_path && Storage::disk('public')->exists($seller->avatar_path)) {
                Storage::disk('public')->delete($seller->avatar_path);
            }
            $seller->avatar_path = $request->file('avatar')->store('sellers', 'public');
        }

        $seller->save();
        ActivityLog::record('seller.updated', $request->user('admin'), $seller);

        return redirect()->route('admin.sellers.index')->with('status', 'Seller updated.');
    }

    public function toggle(Request $request, Seller $seller): RedirectResponse
    {
        $seller->update(['is_active' => !$seller->is_active]);
        ActivityLog::record('seller.toggled', $request->user('admin'), $seller);

        return back()->with('status', 'Seller status updated.');
    }

    public function destroy(Request $request, Seller $seller): RedirectResponse
    {
        if ($seller->avatar_path && Storage::disk('public')->exists($seller->avatar_path)) {
            Storage::disk('public')->delete($seller->avatar_path);
        }

        $seller->delete();
        ActivityLog::record('seller.deleted', $request->user('admin'), $seller);

        return back()->with('status', 'Seller deleted.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:sellers,username,' . ($ignoreId ?? 'NULL') . ',id'],
            'volume' => ['nullable', 'numeric', 'min:0'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'is_verified' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
