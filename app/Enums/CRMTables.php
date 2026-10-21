<?php

declare(strict_types=1);

namespace Modules\CRM\Enums;

use Modules\Core\Enums\Concerns\HasModuleTablesUtils;

enum CRMTables: string
{
    use HasModuleTablesUtils;

    case Counterparties = 'crm_counterparties';
    case Contacts = 'crm_contacts';
    case Contactables = 'crm_contactables';
    case Leads = 'crm_leads';
    case Opportunities = 'crm_opportunities';
    case CounterpartyBankAccounts = 'crm_counterparty_bank_accounts';
}
