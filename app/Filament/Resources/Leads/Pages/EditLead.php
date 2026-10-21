<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Leads\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Utils\HasCloseOrCancelFormAction;
use Modules\CRM\Filament\Resources\Leads\LeadResource;
use Override;

final class EditLead extends EditRecord
{
    use HasCloseOrCancelFormAction;

    #[Override]
    protected static string $resource = LeadResource::class;
}
