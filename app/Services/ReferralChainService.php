<?php

namespace App\Services;

use App\Models\ChainBonusSetting;
use App\Models\ChainCommission;
use App\Models\NftSale;
use App\Models\User;
use Illuminate\Support\Collection;
use RuntimeException;

class ReferralChainService
{
    public function assignSponsorAndSlot(User $newUser, string $refCode): void
    {
        $sponsor = User::where('ref_code', $refCode)->first();
        if (!$sponsor) {
            throw new RuntimeException('Invalid referral code.');
        }

        [$parent, $slot] = $this->findPlacement($sponsor);

        $newUser->referred_by_id = $sponsor->id;
        $newUser->chain_slot = $slot;
        $newUser->chain_path = ($parent->chain_path ?: ($parent->id . '/')) . $parent->id . '/';
        $newUser->save();
    }

    public function findPlacement(User $sponsor): array
    {
        foreach (['A', 'B', 'C'] as $slot) {
            if (!$this->hasChildOnSlot($sponsor, $slot)) {
                return [$sponsor, $slot];
            }
        }

        $queue = new Collection([$sponsor]);
        while ($queue->isNotEmpty()) {
            /** @var User $node */
            $node = $queue->shift();
            foreach (['A', 'B', 'C'] as $slot) {
                if (!$this->hasChildOnSlot($node, $slot)) {
                    return [$node, $slot];
                }
            }
            foreach ($node->referrals()->get() as $child) {
                $queue->push($child);
            }
        }

        throw new RuntimeException('No placement slot available.');
    }

    public function countDirectSlots(User $sponsor): array
    {
        return [
            'A' => $sponsor->referrals()->where('chain_slot', 'A')->count(),
            'B' => $sponsor->referrals()->where('chain_slot', 'B')->count(),
            'C' => $sponsor->referrals()->where('chain_slot', 'C')->count(),
        ];
    }

    public function getAncestors(User $user, int $maxDepth): Collection
    {
        $ancestors = collect();
        $current = $user->sponsor;
        $depth = 1;
        while ($current && $depth <= $maxDepth) {
            $ancestors->push([
                'user' => $current,
                'depth' => $depth,
            ]);
            $current = $current->sponsor;
            $depth++;
        }

        return $ancestors;
    }

    public function distributeCommissionFromSale(NftSale $sale, WalletService $walletService): void
    {
        $settings = ChainBonusSetting::query()
            ->where('is_active', true)
            ->orderBy('depth')
            ->get();

        if ($settings->isEmpty()) {
            return;
        }

        $maxDepth = (int) $settings->max('depth');
        $ancestors = $this->getAncestors($sale->user, $maxDepth);

        foreach ($ancestors as $item) {
            $depth = $item['depth'];
            $ancestor = $item['user'];
            $setting = $settings->firstWhere('depth', $depth);
            if (!$setting) {
                continue;
            }
            $percent = (float) $setting->percent;
            $amount = ($sale->profit_amount * $percent) / 100;
            if ($amount <= 0) {
                continue;
            }

            $walletService->credit($ancestor, 'chain_income', $amount, [
                'source_user_id' => $sale->user_id,
                'depth' => $depth,
                'percent' => $percent,
                'sale_id' => $sale->id,
            ], $sale);

            ChainCommission::create([
                'source_user_id' => $sale->user_id,
                'target_user_id' => $ancestor->id,
                'nft_sale_id' => $sale->id,
                'level_depth' => $depth,
                'percent' => $percent,
                'amount' => $amount,
            ]);
        }
    }

    private function hasChildOnSlot(User $user, string $slot): bool
    {
        return $user->referrals()->where('chain_slot', $slot)->exists();
    }
}
