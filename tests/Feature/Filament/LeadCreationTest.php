<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Core\Tests\Support\HttpContext;
use Modules\CRM\Filament\Resources\Leads\Pages\CreateLead;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Lead;

beforeEach(function (): void {
    HttpContext::panelActorWithoutApproval(new Lead, ['select', 'insert']);
});

it('attaches a new lead to the counterparty that already has the same vat number, whatever its roles', function (): void {
    $supplier = Counterparty::factory()->create(['name' => 'Existing Supplier', 'fiscal_country' => 'IT', 'vat_number' => 'IT01234567890']);
    $supplier->forceFill(['is_supplier' => true])->save();

    Livewire::test(CreateLead::class)
        ->fillForm(['title' => 'Cross-sell to a supplier', 'status' => 'new', 'new_counterparty_name' => 'Typed Name', 'new_counterparty_fiscal_country' => 'it', 'new_counterparty_vat_number' => 'it 01234567890'])
        ->call('create')
        ->assertHasNoFormErrors();

    $lead = Lead::query()->firstOrFail();

    expect($lead->counterparty_id)->toBe($supplier->getKey())
        ->and(Counterparty::query()->count())->toBe(1)
        ->and($supplier->fresh()->name)->toBe('Existing Supplier');
});

it('creates the counterparty of a new lead when nothing matches', function (): void {
    Livewire::test(CreateLead::class)
        ->fillForm(['title' => 'Brand new prospect', 'status' => 'new', 'new_counterparty_name' => 'Fresh Prospect S.r.l.', 'new_counterparty_vat_number' => 'IT03333333333'])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Counterparty::query()->where('vat_number', 'IT03333333333')->count())->toBe(1)
        ->and(Lead::query()->firstOrFail()->counterparty->name)->toBe('Fresh Prospect S.r.l.');
});

it('edits a lead whose counterparty is in the trash', function (): void {
    HttpContext::panelActorWithoutApproval(new Lead, ['select', 'insert', 'update', 'delete']);
    $counterparty = Counterparty::factory()->create(['name' => 'Gone S.r.l.']);
    $lead = Lead::query()->create(['title' => 'Old title', 'status' => 'new', 'counterparty_id' => $counterparty->getKey()]);
    $counterparty->delete();

    $page = Livewire::test(Modules\CRM\Filament\Resources\Leads\Pages\EditLead::class, ['record' => $lead->getKey()]);

    expect($page->instance()->getSchema('form')->getComponent('counterparty_id')->getOptionLabel())->toBe('Gone S.r.l.');

    $page
        ->assertFormSet(['counterparty_id' => $counterparty->getKey()])
        ->fillForm(['title' => 'New title'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($lead->fresh()->title)->toBe('New title')
        ->and($lead->fresh()->counterparty_id)->toBe($counterparty->getKey());
});
