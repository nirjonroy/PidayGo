<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\NftItem;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NftItemController extends Controller
{
    public function index(): View
    {
        return view('admin.nft-items.index', [
            'items' => NftItem::with(['creatorSeller', 'ownerSeller'])->orderByDesc('updated_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.nft-items.form', [
            'item' => new NftItem(),
            'sellers' => Seller::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request, true);
        $validated['auction_end_at'] = $validated['auction_end_at'] ?: null;
        $validated['likes_count'] = $validated['likes_count'] ?? 0;
        $validated['views_count'] = $validated['views_count'] ?? 0;
        $validated['slug'] = $this->makeUniqueSlug($validated['title']);
        $validated['is_trending'] = (bool) ($validated['is_trending'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        $item = new NftItem($validated);

        if ($request->hasFile('image')) {
            $item->image_path = $request->file('image')->store('nfts', 'public');
        }

        $item->save();
        ActivityLog::record('nft.item.created', $request->user('admin'), $item);

        return redirect()->route('admin.nft-items.index')->with('status', 'NFT item created.');
    }

    public function edit(NftItem $nftItem): View
    {
        return view('admin.nft-items.form', [
            'item' => $nftItem,
            'sellers' => Seller::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, NftItem $nftItem): RedirectResponse
    {
        $validated = $this->validatePayload($request, false, $nftItem->id);
        $validated['auction_end_at'] = $validated['auction_end_at'] ?: null;
        $validated['likes_count'] = $validated['likes_count'] ?? $nftItem->likes_count;
        $validated['views_count'] = $validated['views_count'] ?? $nftItem->views_count;

        if ($validated['title'] !== $nftItem->title) {
            $validated['slug'] = $this->makeUniqueSlug($validated['title'], $nftItem->id);
        }

        $validated['is_trending'] = (bool) ($validated['is_trending'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        $nftItem->fill($validated);

        if ($request->hasFile('image')) {
            if ($nftItem->image_path && Storage::disk('public')->exists($nftItem->image_path)) {
                Storage::disk('public')->delete($nftItem->image_path);
            }
            $nftItem->image_path = $request->file('image')->store('nfts', 'public');
        }

        $nftItem->save();
        ActivityLog::record('nft.item.updated', $request->user('admin'), $nftItem);

        return redirect()->route('admin.nft-items.index')->with('status', 'NFT item updated.');
    }

    public function destroy(Request $request, NftItem $nftItem): RedirectResponse
    {
        if ($nftItem->image_path && Storage::disk('public')->exists($nftItem->image_path)) {
            Storage::disk('public')->delete($nftItem->image_path);
        }

        $nftItem->delete();
        ActivityLog::record('nft.item.deleted', $request->user('admin'), $nftItem);

        return back()->with('status', 'NFT item deleted.');
    }

    private function validatePayload(Request $request, bool $requireImage, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'creator_seller_id' => ['nullable', 'exists:sellers,id'],
            'owner_seller_id' => ['nullable', 'exists:sellers,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'auction_end_at' => ['nullable', 'date'],
            'likes_count' => ['nullable', 'integer', 'min:0'],
            'views_count' => ['nullable', 'integer', 'min:0'],
            'is_trending' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,published'],
            'image' => array_filter([
                $requireImage ? 'required' : 'nullable',
                'image',
                'mimes:png,jpg,jpeg,webp',
                'max:4096',
            ]),
        ]);
    }

    private function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (NftItem::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }
}
