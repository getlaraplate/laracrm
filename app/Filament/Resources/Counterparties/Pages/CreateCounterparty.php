<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Counterparties\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Utils\HasCloseOrCancelFormAction;
use Modules\CRM\Filament\Resources\Counterparties\CounterpartyResource;
use Override;

final class CreateCounterparty extends CreateRecord
{
    use HasCloseOrCancelFormAction;

    #[Override]
    protected static string $resource = CounterpartyResource::class;
}
