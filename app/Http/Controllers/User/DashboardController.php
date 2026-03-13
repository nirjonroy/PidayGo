<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChainCommission;
use App\Models\NftSale;
use App\Models\User;
use App\Services\StakeRewardService;
use App\Services\UserLevelResolver;
use App\Services\WalletService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const INCOME_LEDGER_TYPES = [
        'nft_profit',
        'chain_income',
        'reward_credit',
    ];

    private const INCOME_BREAKDOWN = [
        'comprehensive' => [
            'label' => 'Comprehensive',
            'types' => self::INCOME_LEDGER_TYPES,
        ],
        'reserve' => [
            'label' => 'Reserve',
            'types' => ['nft_profit'],
        ],
        'team' => [
            'label' => 'Team',
            'types' => ['chain_income'],
        ],
        'activity' => [
            'label' => 'Activity',
            'types' => [],
        ],
        'bid' => [
            'label' => 'Bid',
            'types' => [],
        ],
        'stake' => [
            'label' => 'Stake',
            'types' => ['reward_credit'],
        ],
    ];

    public function __invoke(
        Request $request,
        UserLevelResolver $levelResolver,
        WalletService $walletService,
        StakeRewardService $stakeRewardService
    ): View {
        $user = $request->user();

        $stakeRewardService->creditDueRewardsForUser($user, $walletService);
        $user->loadMissing(['profile', 'latestKycRequest']);

        $displayName = filled($user->profile?->username)
            ? $user->profile->username
            : $user->name;

        $totalIncomeByType = $user->walletLedgers()
            ->selectRaw('type, SUM(amount) as total_amount')
            ->whereIn('type', self::INCOME_LEDGER_TYPES)
            ->groupBy('type')
            ->pluck('total_amount', 'type');

        $dailyIncomeByType = $user->walletLedgers()
            ->selectRaw('type, SUM(amount) as total_amount')
            ->whereIn('type', self::INCOME_LEDGER_TYPES)
            ->whereDate('created_at', today())
            ->groupBy('type')
            ->pluck('total_amount', 'type');

        $incomeBreakdown = collect(self::INCOME_BREAKDOWN)
            ->map(function (array $config) use ($dailyIncomeByType, $totalIncomeByType) {
                $types = $config['types'];

                return [
                    'label' => $config['label'],
                    'daily' => collect($types)->sum(fn (string $type) => (float) ($dailyIncomeByType[$type] ?? 0)),
                    'total' => collect($types)->sum(fn (string $type) => (float) ($totalIncomeByType[$type] ?? 0)),
                ];
            })
            ->values();

        $dailyIncome = (float) $incomeBreakdown->firstWhere('label', 'Comprehensive')['daily'];
        $totalIncome = (float) $incomeBreakdown->firstWhere('label', 'Comprehensive')['total'];
        [$teamSummary, $teamBranches, $teamDefaultBranch] = $this->buildTeamData($user);

        return view('dashboard', [
            'user' => $user,
            'displayName' => $displayName,
            'avatarUrl' => $user->profile?->photo_url,
            'level' => $levelResolver->resolve($user),
            'walletBalance' => (float) $walletService->getBalance($user),
            'reserveBalance' => (float) $user->reserves()
                ->where('status', 'confirmed')
                ->sum('amount'),
            'dailyIncome' => $dailyIncome,
            'totalIncome' => $totalIncome,
            'incomeBreakdown' => $incomeBreakdown,
            'teamSummary' => $teamSummary,
            'teamBranches' => $teamBranches,
            'teamDefaultBranch' => $teamDefaultBranch,
            'recentWalletLedgers' => $user->walletLedgers()
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
            'emailVerified' => !is_null($user->email_verified_at),
            'twoFactorEnabled' => $user->hasTwoFactorEnabled(),
            'kycStatus' => $user->latestKycRequest?->status,
        ]);
    }

    private function buildTeamData(User $user): array
    {
        $teamMembers = User::query()
            ->with('profile')
            ->where('id', '!=', $user->id)
            ->where(function ($query) use ($user) {
                $query->where('chain_path', 'like', $user->id . '/%')
                    ->orWhere('chain_path', 'like', '%/' . $user->id . '/%');
            })
            ->orderBy('created_at')
            ->get();

        $directBranchRoots = $teamMembers
            ->filter(function (User $member) use ($user) {
                return $this->lastChainAncestorId($member->chain_path) === $user->id
                    && in_array($member->chain_slot, ['A', 'B', 'C'], true);
            })
            ->unique('chain_slot')
            ->keyBy('chain_slot');

        $memberIds = $teamMembers->modelKeys();
        $commissionSummaries = collect();
        $dailyCommissionTotals = collect();
        $saleSummaries = collect();

        if (!empty($memberIds)) {
            $commissionSummaries = ChainCommission::query()
                ->selectRaw('source_user_id, SUM(amount) as total_amount, COUNT(*) as commission_count, MAX(created_at) as last_commission_at')
                ->where('target_user_id', $user->id)
                ->whereIn('source_user_id', $memberIds)
                ->groupBy('source_user_id')
                ->get()
                ->keyBy('source_user_id');

            $dailyCommissionTotals = ChainCommission::query()
                ->selectRaw('source_user_id, SUM(amount) as total_amount')
                ->where('target_user_id', $user->id)
                ->whereIn('source_user_id', $memberIds)
                ->whereDate('created_at', today())
                ->groupBy('source_user_id')
                ->pluck('total_amount', 'source_user_id');

            $saleSummaries = NftSale::query()
                ->selectRaw('user_id, COUNT(*) as sales_count, SUM(sale_amount) as total_sales_amount, SUM(profit_amount) as total_profit_amount, MAX(created_at) as last_sale_at')
                ->whereIn('user_id', $memberIds)
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');
        }

        $membersByBranch = [
            'A' => collect(),
            'B' => collect(),
            'C' => collect(),
        ];

        foreach ($teamMembers as $member) {
            $branchSlot = $this->detectBranchSlot($member, $directBranchRoots, $user->id);
            if (!$branchSlot) {
                continue;
            }

            $commissionSummary = $commissionSummaries->get($member->id);
            $saleSummary = $saleSummaries->get($member->id);

            $lastActivity = collect([
                $commissionSummary?->last_commission_at,
                $saleSummary?->last_sale_at,
            ])->filter()->map(fn ($value) => Carbon::parse($value))->sortDesc()->first();

            $membersByBranch[$branchSlot]->push([
                'id' => $member->id,
                'display_name' => filled($member->profile?->username) ? $member->profile->username : $member->name,
                'email' => $member->email,
                'phone' => $member->profile?->phone,
                'uid' => $member->user_code,
                'ref_code' => $member->ref_code,
                'joined_at' => optional($member->created_at)->format('M d, Y'),
                'total_earned' => (float) ($commissionSummary?->total_amount ?? 0),
                'daily_earned' => (float) ($dailyCommissionTotals[$member->id] ?? 0),
                'commission_count' => (int) ($commissionSummary?->commission_count ?? 0),
                'sales_count' => (int) ($saleSummary?->sales_count ?? 0),
                'sales_amount' => (float) ($saleSummary?->total_sales_amount ?? 0),
                'profit_amount' => (float) ($saleSummary?->total_profit_amount ?? 0),
                'last_activity' => $lastActivity?->format('M d, Y h:i A'),
            ]);
        }

        $teamBranches = collect(['A', 'B', 'C'])
            ->map(function (string $slot) use ($membersByBranch) {
                $members = $membersByBranch[$slot]
                    ->sortByDesc('total_earned')
                    ->values();

                return [
                    'slot' => $slot,
                    'label' => $slot . ' Chain',
                    'count' => $members->count(),
                    'total_earned' => (float) $members->sum('total_earned'),
                    'members' => $members,
                ];
            })
            ->values();

        $defaultBranchData = $teamBranches->first(fn (array $branch) => $branch['count'] > 0);
        $defaultBranch = $defaultBranchData['slot'] ?? 'A';

        return [[
            'total_members' => (int) $teamBranches->sum('count'),
            'a_members' => (int) ($teamBranches->firstWhere('slot', 'A')['count'] ?? 0),
            'b_members' => (int) ($teamBranches->firstWhere('slot', 'B')['count'] ?? 0),
            'c_members' => (int) ($teamBranches->firstWhere('slot', 'C')['count'] ?? 0),
        ], $teamBranches, $defaultBranch];
    }

    private function detectBranchSlot(User $member, Collection $directBranchRoots, int $currentUserId): ?string
    {
        if ($this->lastChainAncestorId($member->chain_path) === $currentUserId && in_array($member->chain_slot, ['A', 'B', 'C'], true)) {
            return $member->chain_slot;
        }

        $path = '/' . trim((string) $member->chain_path, '/') . '/';

        foreach (['A', 'B', 'C'] as $slot) {
            $root = $directBranchRoots->get($slot);
            if (!$root) {
                continue;
            }

            if ($member->id === $root->id || str_contains($path, '/' . $root->id . '/')) {
                return $slot;
            }
        }

        return null;
    }

    private function lastChainAncestorId(?string $chainPath): ?int
    {
        $segments = array_values(array_filter(explode('/', trim((string) $chainPath, '/'))));

        if (empty($segments)) {
            return null;
        }

        return (int) end($segments);
    }
}
