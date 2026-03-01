<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class ReferralPlacementService
{
    public function placeUser(User $newUser, User $sponsor): void
    {
        $parent = $this->findPlacementParent($sponsor);

        $newUser->sponsor_id = $sponsor->id;
        $newUser->placement_parent_id = $parent['parent']->id;
        $newUser->placement_side = $parent['side'];
        $newUser->depth = $parent['parent']->depth + 1;
        $newUser->save();
    }

    private function findPlacementParent(User $sponsor): array
    {
        if (!$this->hasChildOnSide($sponsor, 'B')) {
            return ['parent' => $sponsor, 'side' => 'B'];
        }

        if (!$this->hasChildOnSide($sponsor, 'C')) {
            return ['parent' => $sponsor, 'side' => 'C'];
        }

        $queue = new Collection([$sponsor]);
        while ($queue->isNotEmpty()) {
            /** @var User $node */
            $node = $queue->shift();
            $children = $node->placements()->get();

            if (!$this->hasChildOnSide($node, 'B')) {
                return ['parent' => $node, 'side' => 'B'];
            }

            if (!$this->hasChildOnSide($node, 'C')) {
                return ['parent' => $node, 'side' => 'C'];
            }

            foreach ($children as $child) {
                $queue->push($child);
            }
        }

        return ['parent' => $sponsor, 'side' => 'B'];
    }

    private function hasChildOnSide(User $user, string $side): bool
    {
        return $user->placements()->where('placement_side', $side)->exists();
    }
}
