<?php

declare(strict_types=1);

namespace Modules\CRM\Services;

use Illuminate\Validation\ValidationException;
use Modules\CRM\Casts\LeadStatus;
use Modules\CRM\Casts\OpportunityStatus;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Opportunity;

/**
 * Turns a qualified lead into an open opportunity. The lead keeps its row, becomes `converted` and is
 * linked from the opportunity, so the pipeline report starts from the opportunity and the lead list still
 * shows where it went. Everything happens in one transaction: a failed opportunity leaves the lead as it was.
 */
final class LeadConversionService
{
    /**
     * The expected value is unknown at conversion; the currencies and the rate start from the column defaults
     * and are edited on the opportunity.
     */
    private const string DEFAULT_CURRENCY = 'EUR';

    /**
     * @param  int  $stage_taxonomy_id  The pipeline stage the opportunity starts in.
     * @param  ?string  $name  Defaults to the lead title.
     * @param  ?Counterparty  $counterparty  Required only when the lead has none; it is then linked to the lead too.
     *
     * @throws ValidationException When the lead is already converted, disqualified, or has no counterparty to convert with.
     */
    public function convert(Lead $lead, int $stage_taxonomy_id, ?string $name = null, ?Counterparty $counterparty = null): Opportunity
    {
        if (in_array($lead->status, [LeadStatus::Converted, LeadStatus::Disqualified], true)) {
            throw ValidationException::withMessages([
                'status' => ['A converted or disqualified lead cannot be converted.'],
            ]);
        }

        $counterparty_id = $lead->counterparty_id ?? $counterparty?->getKey();

        if ($counterparty_id === null) {
            throw ValidationException::withMessages([
                'counterparty_id' => ['A lead needs a counterparty to become an opportunity.'],
            ]);
        }

        return $lead->getConnection()->transaction(static function () use ($lead, $stage_taxonomy_id, $name, $counterparty_id): Opportunity {
            /** @var Opportunity $opportunity */
            $opportunity = $lead->opportunities()->create([
                'counterparty_id' => $counterparty_id,
                'stage_taxonomy_id' => $stage_taxonomy_id,
                'name' => $name !== null && mb_trim($name) !== '' ? mb_trim($name) : $lead->title,
                'status' => OpportunityStatus::Open,
                'expected_currency_doc' => self::DEFAULT_CURRENCY,
                'expected_currency_local' => self::DEFAULT_CURRENCY,
                'expected_fx_rate' => 1,
            ]);

            $lead->counterparty_id = $counterparty_id;
            $lead->status = LeadStatus::Converted;
            $lead->converted_at = now();
            $lead->save();

            return $opportunity;
        });
    }
}
