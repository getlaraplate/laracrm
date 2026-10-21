<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use Modules\CRM\Casts\LeadStatus;
use Modules\CRM\Casts\OpportunityStatus;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Opportunity;
use Modules\CRM\Services\LeadConversionService;
use Modules\CRM\Tests\Support\OpportunityStageTaxonomy;

function convertibleLead(array $attributes = []): Lead
{
    return Lead::query()->create([
        'title' => 'Inbound widget inquiry',
        'status' => LeadStatus::Qualified,
        'counterparty_id' => Counterparty::factory()->create()->getKey(),
        ...$attributes,
    ]);
}

it('turns a lead into an open opportunity on the same counterparty', function (): void {
    $lead = convertibleLead();
    $stage = OpportunityStageTaxonomy::insertMinimalId('convert-stage');

    $opportunity = resolve(LeadConversionService::class)->convert($lead, $stage);

    expect($opportunity->lead_id)->toBe($lead->getKey())
        ->and($opportunity->counterparty_id)->toBe($lead->counterparty_id)
        ->and($opportunity->stage_taxonomy_id)->toBe($stage)
        ->and($opportunity->name)->toBe('Inbound widget inquiry')
        ->and($opportunity->status)->toBe(OpportunityStatus::Open)
        ->and($lead->fresh()->status)->toBe(LeadStatus::Converted)
        ->and($lead->fresh()->converted_at)->not->toBeNull();
});

it('names the opportunity as asked', function (): void {
    $opportunity = resolve(LeadConversionService::class)
        ->convert(convertibleLead(), OpportunityStageTaxonomy::insertMinimalId('convert-name'), 'Warehouse rollout');

    expect($opportunity->name)->toBe('Warehouse rollout');
});

it('takes the counterparty from the caller when the lead has none and links it to the lead', function (): void {
    $lead = convertibleLead(['counterparty_id' => null]);
    $counterparty = Counterparty::factory()->create();

    $opportunity = resolve(LeadConversionService::class)
        ->convert($lead, OpportunityStageTaxonomy::insertMinimalId('convert-given'), counterparty: $counterparty);

    expect($opportunity->counterparty_id)->toBe($counterparty->getKey())
        ->and($lead->fresh()->counterparty_id)->toBe($counterparty->getKey());
});

it('refuses a lead without a counterparty when none is given', function (): void {
    $lead = convertibleLead(['counterparty_id' => null]);
    $stage = OpportunityStageTaxonomy::insertMinimalId('convert-none');

    expect(fn () => resolve(LeadConversionService::class)->convert($lead, $stage))->toThrow(ValidationException::class)
        ->and($lead->fresh()->status)->toBe(LeadStatus::Qualified)
        ->and(Opportunity::query()->count())->toBe(0);
});

it('refuses a lead that is already converted or disqualified', function (LeadStatus $status): void {
    $lead = convertibleLead(['status' => $status]);
    $stage = OpportunityStageTaxonomy::insertMinimalId('convert-' . $status->value);

    expect(fn () => resolve(LeadConversionService::class)->convert($lead, $stage))->toThrow(ValidationException::class)
        ->and(Opportunity::query()->count())->toBe(0);
})->with([LeadStatus::Converted, LeadStatus::Disqualified]);

it('leaves the lead untouched when the opportunity cannot be created', function (): void {
    $lead = convertibleLead();

    expect(fn () => resolve(LeadConversionService::class)->convert($lead, 999999))->toThrow(Exception::class)
        ->and($lead->fresh()->status)->toBe(LeadStatus::Qualified)
        ->and($lead->fresh()->converted_at)->toBeNull();
});
