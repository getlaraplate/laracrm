<?php

declare(strict_types=1);

namespace Modules\CRM\Tests\Support;

/**
 * Stand-in status object for pipeline rows with a value outside OpportunityStatus.
 */
final class OpportunityStatusDouble
{
    public function __construct(
        public string $value,
    ) {}
}
