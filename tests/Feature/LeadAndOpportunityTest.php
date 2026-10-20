<?php

declare(strict_types=1);

use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Modules\CRM\Casts\LeadStatus;
use Modules\CRM\Casts\OpportunityStatus;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Opportunity;
use Modules\CRM\Services\OpportunityLifecycleService;
use Modules\CRM\Tests\Support\OpportunityStageTaxonomy;

function crmOpportunity(array $attributes = []): Opportunity
{
    return Opportunity::query()->create([
        'counterparty_id' => Counterparty::factory()->create()->getKey(),
        'stage_taxonomy_id' => OpportunityStageTaxonomy::insertMinimalId('crm-opp-' . uniqid()),
        'name' => 'Enterprise rollout',
        'status' => OpportunityStatus::Open,
        'expected_currency_doc' => 'EUR',
        'expected_currency_local' => 'EUR',
        'expected_fx_rate' => 1,
        ...$attributes,
    ]);
}

it('creates a lead without a counterparty', function (): void {
    $lead = Lead::query()->create(['title' => 'Inbound widget inquiry', 'status' => LeadStatus::New, 'source' => 'web']);

    expect($lead->counterparty_id)->toBeNull()
        ->and($lead->status)->toBe(LeadStatus::New);
});

it('links a lead to a counterparty and a contact', function (): void {
    $counterparty = Counterparty::factory()->create();
    $contact = Contact::factory()->create();

    $lead = Lead::query()->create([
        'title' => 'Trade fair contact',
        'status' => LeadStatus::Contacted,
        'counterparty_id' => $counterparty->getKey(),
        'contact_id' => $contact->getKey(),
    ]);

    expect($lead->counterparty->is($counterparty))->toBeTrue()
        ->and($lead->contact->is($contact))->toBeTrue()
        ->and($contact->leads()->count())->toBe(1)
        ->and($counterparty->leads()->count())->toBe(1);
});

it('requires a counterparty for an opportunity', function (): void {
    $stage_id = OpportunityStageTaxonomy::insertMinimalId('crm-opp-required');

    expect(fn () => Opportunity::query()->create([
        'stage_taxonomy_id' => $stage_id,
        'name' => 'Orphan deal',
        'status' => OpportunityStatus::Open,
        'expected_currency_doc' => 'EUR',
        'expected_currency_local' => 'EUR',
        'expected_fx_rate' => 1,
    ]))->toThrow(ValidationException::class);
});

it('keeps the opportunity visible when its counterparty is soft deleted', function (): void {
    $opportunity = crmOpportunity();

    $opportunity->counterparty->delete();

    $reloaded = Opportunity::query()->findOrFail($opportunity->getKey());

    expect($reloaded->counterparty?->trashed())->toBeTrue()
        ->and($reloaded->counterparty->name)->not->toBeEmpty();
});

it('refuses to hard delete a counterparty that has an opportunity', function (): void {
    $opportunity = crmOpportunity();

    expect(fn () => $opportunity->counterparty->forceDelete())->toThrow(QueryException::class);
});

it('links an opportunity to its lead', function (): void {
    $lead = Lead::query()->create(['title' => 'Qualified lead', 'status' => LeadStatus::Qualified]);
    $opportunity = crmOpportunity(['lead_id' => $lead->getKey()]);

    expect($opportunity->lead->is($lead))->toBeTrue()
        ->and($lead->opportunities()->count())->toBe(1);
});

it('marks an opportunity won once and keeps the first win date', function (): void {
    $opportunity = crmOpportunity();
    $service = resolve(OpportunityLifecycleService::class);

    $service->markWon($opportunity->getKey());
    $first_won_at = $opportunity->fresh()->won_at;

    $this->travel(1)->hour();
    $service->markWon($opportunity->getKey());

    expect($opportunity->fresh()->status)->toBe(OpportunityStatus::Won)
        ->and($first_won_at)->not->toBeNull()
        ->and($opportunity->fresh()->won_at->equalTo($first_won_at))->toBeTrue();
});

it('ignores a missing opportunity when marking won', function (): void {
    resolve(OpportunityLifecycleService::class)->markWon(999999);

    expect(Opportunity::query()->count())->toBe(0);
});
