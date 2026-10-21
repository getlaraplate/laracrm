<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Opportunities\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Utils\HasRecords;
use Modules\CRM\Filament\Resources\Opportunities\OpportunityResource;
use Override;

final class ListOpportunities extends ListRecords
{
    use HasRecords;

    #[Override]
    protected static string $resource = OpportunityResource::class;
}
