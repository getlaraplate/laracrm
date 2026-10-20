<?php

declare(strict_types=1);

namespace Modules\CRM\Services;

use Modules\CRM\Casts\OpportunityStatus;
use Modules\CRM\Models\Opportunity;

final class OpportunityLifecycleService
{
    /**
     * Marks the opportunity won, once: a second call keeps the first win date. An unknown id is ignored,
     * so a caller that learns about a win from another module does not need to know whether the CRM still has the deal.
     */
    public function markWon(int|string $opportunity_id): void
    {
        $opportunity = Opportunity::query()->find($opportunity_id);

        if (! $opportunity instanceof Opportunity) {
            return;
        }

        if ($opportunity->status === OpportunityStatus::Won && $opportunity->won_at !== null) {
            return;
        }

        $opportunity->status = OpportunityStatus::Won;
        $opportunity->won_at ??= now();
        $opportunity->saveQuietly();
    }
}
