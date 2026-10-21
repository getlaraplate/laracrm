<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Counterparties\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\CRM\Filament\Resources\Counterparties\CounterpartyResource;
use Override;

final class ListCounterparties extends ListRecords
{
    #[Override]
    protected static string $resource = CounterpartyResource::class;
}
