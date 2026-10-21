<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Counterparties\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\CRM\Filament\Resources\Counterparties\CounterpartyResource;
use Override;

final class EditCounterparty extends EditRecord
{
    #[Override]
    protected static string $resource = CounterpartyResource::class;
}
