<?php

namespace Modules\Affiliate\Console\Commands;

use Illuminate\Console\Command;
use Modules\Affiliate\Services\AffiliateCommissionService;

class ReleaseAffiliateCommissionsCommand extends Command
{
    protected $signature = 'affiliate:release-commissions';

    protected $description = 'Release pending affiliate commissions after hold period';

    public function handle(AffiliateCommissionService $commissionService): int
    {
        $count = $commissionService->releaseAvailableCommissions();

        $this->info("Released {$count} affiliate commissions.");

        return self::SUCCESS;
    }
}