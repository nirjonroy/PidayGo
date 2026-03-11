<?php

namespace App\Console\Commands;

use App\Services\StakeRewardService;
use App\Services\WalletService;
use Illuminate\Console\Command;

class CreditDailyStakeRewards extends Command
{
    protected $signature = 'stakes:credit-daily-rewards';

    protected $description = 'Credit daily staking rewards for active stakes.';

    public function handle(WalletService $walletService, StakeRewardService $stakeRewardService): int
    {
        $stakeRewardService->creditDueRewardsForAll($walletService);

        $this->info('Daily stake rewards processed.');

        return self::SUCCESS;
    }
}
