<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Leads\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Utils\HasCloseOrCancelFormAction;
use Modules\CRM\Filament\Resources\Leads\LeadResource;
use Modules\CRM\Models\Counterparty;
use Override;

final class CreateLead extends CreateRecord
{
    use HasCloseOrCancelFormAction;

    #[Override]
    protected static string $resource = LeadResource::class;

    /**
     * A lead typed for a company that is not picked from the list attaches to the counterparty with the same
     * fiscal identity (or name), suppliers included, and creates one only when nothing matches.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $typed = array_filter([
            'name' => $data['new_counterparty_name'] ?? null,
            'fiscal_country' => $data['new_counterparty_fiscal_country'] ?? null,
            'vat_number' => $data['new_counterparty_vat_number'] ?? null,
            'tax_id' => $data['new_counterparty_tax_id'] ?? null,
        ], static fn (mixed $value): bool => filled($value));

        unset($data['new_counterparty_name'], $data['new_counterparty_fiscal_country'], $data['new_counterparty_vat_number'], $data['new_counterparty_tax_id']);

        if (blank($data['counterparty_id'] ?? null) && $typed !== []) {
            $data['counterparty_id'] = Counterparty::attachOrCreate([...$typed, 'name' => $typed['name'] ?? ($typed['vat_number'] ?? $typed['tax_id'])])->getKey();
        }

        return $data;
    }
}
