<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Counterparties\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\CRM\Filament\Resources\Counterparties\CounterpartyResource;
use Override;

final class CreateCounterparty extends CreateRecord
{
    #[Override]
    protected static string $resource = CounterpartyResource::class;
}
