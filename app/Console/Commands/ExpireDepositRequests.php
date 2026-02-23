<?php

namespace App\Console\Commands;

use App\Models\DepositRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireDepositRequests extends Command
{
    protected $signature = 'deposits:expire-pending';

    protected $description = 'Mark pending deposits as expired when past expires_at.';

    public function handle(): int
    {
        $now = now();

        DepositRequest::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->orderBy('id')
            ->chunkById(100, function ($items) use ($now) {
                foreach ($items as $item) {
                    DB::transaction(function () use ($item, $now) {
                        $locked = DepositRequest::whereKey($item->id)->lockForUpdate()->first();

                        if (!$locked || $locked->status !== 'pending') {
                            return;
                        }

                        if ($locked->expires_at && $locked->expires_at->lt($now)) {
                            $locked->update([
                                'status' => 'expired',
                                'reviewed_at' => $now,
                                'admin_note' => 'Expired automatically.',
                            ]);
                        }
                    });
                }
            });

        $this->info('Expired pending deposits processed.');

        return self::SUCCESS;
    }
}
