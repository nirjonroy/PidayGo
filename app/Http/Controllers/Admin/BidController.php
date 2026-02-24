<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bid;
use App\Models\NftItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BidController extends Controller
{
    public function index(): View
    {
        return view('admin.bids.index', [
            'bids' => Bid::with(['item', 'user'])->orderByDesc('created_at')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.bids.form', [
            'bid' => new Bid(),
            'items' => NftItem::orderBy('title')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        if (empty($validated['user_id']) && empty($validated['bidder_name'])) {
            return back()->withErrors(['bidder_name' => 'Bidder name is required when no user is selected.'])->withInput();
        }
        if (!empty($validated['user_id'])) {
            $validated['bidder_name'] = null;
        }

        $bid = Bid::create($validated);
        ActivityLog::record('bid.created', $request->user('admin'), $bid);

        return redirect()->route('admin.bids.index')->with('status', 'Bid created.');
    }

    public function destroy(Request $request, Bid $bid): RedirectResponse
    {
        $bid->delete();
        ActivityLog::record('bid.deleted', $request->user('admin'), $bid);

        return back()->with('status', 'Bid deleted.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'nft_item_id' => ['required', 'exists:nft_items,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'bidder_name' => ['nullable', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:0.00000001'],
        ]);
    }
}
