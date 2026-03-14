<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChainCommission;
use App\Models\User;
use App\Services\ReferralChainService;
use App\Services\StakeRewardService;
use App\Services\UserLevelResolver;
use App\Services\WalletService;
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
        ReferralChainService $referralChainService,
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
        [$teamSummary, $teamBranches, $teamDefaultBranch] = $this->buildTeamData($user, $referralChainService);

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

    private function buildTeamData(User $user, ReferralChainService $referralChainService): array
    {
        $referralLayers = $referralChainService->getReferralLayers($user, 3, ['profile']);

        $branchModels = [
            'A' => $referralLayers->get(1, collect()),
            'B' => $referralLayers->get(2, collect()),
            'C' => $referralLayers->get(3, collect()),
        ];

        $memberIds = collect($branchModels)
            ->flatMap(fn ($members) => $members->pluck('id'))
            ->values();

        $commissionSummaries = collect();

        if ($memberIds->isNotEmpty()) {
            $commissionSummaries = ChainCommission::query()
                ->selectRaw('source_user_id, SUM(amount) as total_amount')
                ->where('target_user_id', $user->id)
                ->whereIn('source_user_id', $memberIds->all())
                ->groupBy('source_user_id')
                ->get()
                ->keyBy('source_user_id');
        }

        $teamBranches = collect(['A', 'B', 'C'])
            ->map(function (string $slot) use ($branchModels, $commissionSummaries) {
                $members = $branchModels[$slot]
                    ->map(function (User $member) use ($commissionSummaries) {
                        $commissionSummary = $commissionSummaries->get($member->id);

                        return [
                            'id' => $member->id,
                            'display_name' => filled($member->profile?->username) ? $member->profile->username : $member->name,
                            'uid' => $member->user_code,
                            'total_earned' => (float) ($commissionSummary?->total_amount ?? 0),
                        ];
                    })
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
}
