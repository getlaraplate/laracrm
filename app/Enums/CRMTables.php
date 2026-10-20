<?php

declare(strict_types=1);

namespace Modules\CRM\Enums;

use Modules\Core\Enums\Concerns\HasModuleTablesUtils;

enum CRMTables: string
{
    use HasModuleTablesUtils;

    case Counterparties = 'crm_counterparties';
}
