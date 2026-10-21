<?php

declare(strict_types=1);

use App\Models\User;
use Livewire\Livewire;
use Modules\Core\Models\Role;
use Modules\CRM\Casts\LeadStatus;
use Modules\CRM\Filament\Resources\Leads\Pages\ListLeads;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Opportunity;
use Modules\CRM\Tests\Support\OpportunityStageTaxonomy;

function signInAsLeadAdmin(): void
{
    $admin = User::factory()->create(['email_verified_at' => now(), 'password' => 'Aa1!FilamentAdminPass']);
    $admin->roles()->attach(Role::factory()->create(['name' => 'superadmin', 'guard_name' => 'web']));
    test()->actingAs($admin, 'admin');
}

it('converts a lead from the list', function (): void {
    signInAsLeadAdmin();
    $stage = OpportunityStageTaxonomy::insertMinimalId('action-stage');
    $lead = Lead::query()->create(['title' => 'Trade fair contact', 'status' => LeadStatus::Qualified, 'counterparty_id' => Counterparty::factory()->create()->getKey()]);

    Livewire::test(ListLeads::class)
        ->loadTable()
        ->callTableAction('convert', $lead, ['stage_taxonomy_id' => $stage, 'name' => 'Trade fair deal'])
        ->assertHasNoTableActionErrors();

    expect(Opportunity::query()->where('lead_id', $lead->getKey())->value('name'))->toBe('Trade fair deal')
        ->and($lead->fresh()->status)->toBe(LeadStatus::Converted);
});

it('hides the conversion for a lead that is already converted', function (): void {
    signInAsLeadAdmin();
    $lead = Lead::query()->create(['title' => 'Done', 'status' => LeadStatus::Converted]);

    Livewire::test(ListLeads::class)
        ->loadTable()
        ->assertTableActionHidden('convert', $lead);
});
