<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Panel;
use Livewire\Livewire;
use Modules\Core\Models\Role;
use Modules\Core\Tests\Support\HttpContext;
use Modules\CRM\Filament\CRMPlugin;
use Modules\CRM\Filament\Resources\Contacts\ContactResource;
use Modules\CRM\Filament\Resources\Counterparties\CounterpartyResource;
use Modules\CRM\Filament\Resources\Counterparties\Pages\ListCounterparties;
use Modules\CRM\Filament\Resources\Leads\LeadResource;
use Modules\CRM\Filament\Resources\Opportunities\OpportunityResource;
use Modules\CRM\Models\Counterparty;

it('exposes crm plugin metadata and boots without error', function (): void {
    $plugin = new CRMPlugin();

    expect($plugin->getId())->toBe('crm')
        ->and($plugin->getModuleName())->toBe('CRM');

    $plugin->boot(Panel::make('crm-test'));
});

it('registers every navigation group used by the crm resources and pages', function (): void {
    $panel = Panel::make('crm-test');
    (new CRMPlugin())->afterRegister($panel);

    $registered = array_keys($panel->getNavigationGroups());

    $used = collect(glob(module_path('CRM', 'app/Filament/{Resources/*,Pages}/*.php'), GLOB_BRACE))
        ->map(fn (string $file): string => 'Modules\\CRM\\Filament\\' . str_replace(['/', '.php'], ['\\', ''], mb_substr($file, mb_strlen(module_path('CRM', 'app/Filament/')))))
        ->filter(fn (string $class): bool => class_exists($class) && property_exists($class, 'navigationGroup'))
        ->map(function (string $class): ?string {
            $property = new ReflectionProperty($class, 'navigationGroup');

            return $property->getValue();
        })
        ->filter()
        ->unique()
        ->values()
        ->all();

    expect($used)->not->toBeEmpty()
        ->and(array_diff($used, $registered))->toBe([]);
});

it('renders the crm resources and lists counterparties for an authenticated admin', function (): void {
    $admin = User::factory()->create([
        'email' => 'crm-filament-smoke@example.com',
        'email_verified_at' => now(),
        'password' => 'Aa1!FilamentAdminPass',
    ]);
    $admin->roles()->attach(Role::factory()->create(['name' => 'superadmin', 'guard_name' => 'web']));
    $this->actingAs($admin, 'admin');

    $customer = Counterparty::factory()->create(['name' => 'Acme Customer S.r.l.']);
    $customer->forceFill(['is_customer' => true])->save();
    $pure_supplier = Counterparty::factory()->create(['name' => 'Only Supplier S.p.A.']);
    $pure_supplier->forceFill(['is_supplier' => true])->save();

    $this->get(CounterpartyResource::getUrl('index'))->assertOk();
    $this->get(CounterpartyResource::getUrl('edit', ['record' => $pure_supplier]))->assertOk()->assertSee('Only Supplier S.p.A.');
    $this->get(ContactResource::getUrl('index'))->assertOk();
    $this->get(LeadResource::getUrl('index'))->assertOk();
    $this->get(OpportunityResource::getUrl('index'))->assertOk();
    $this->get(LeadResource::getUrl('create'))->assertOk();
    $this->get(OpportunityResource::getUrl('create'))->assertOk();
});

it('lists counterparties with customer and supplier flags and hides pure suppliers by default', function (): void {
    HttpContext::panelActorWithoutApproval(new Counterparty, ['select']);

    $customer = Counterparty::factory()->create();
    $customer->forceFill(['is_customer' => true])->save();
    $both = Counterparty::factory()->create();
    $both->forceFill(['is_customer' => true, 'is_supplier' => true])->save();
    $pure_supplier = Counterparty::factory()->create();
    $pure_supplier->forceFill(['is_supplier' => true])->save();
    $prospect = Counterparty::factory()->create();

    Livewire::test(ListCounterparties::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$customer, $both, $prospect])
        ->assertCanNotSeeTableRecords([$pure_supplier])
        ->assertTableColumnStateSet('is_supplier', true, $both)
        ->filterTable('hide_pure_suppliers', false)
        ->assertCanSeeTableRecords([$customer, $both, $prospect, $pure_supplier]);
});

it('defines the Filament pages and the counterparty linkage field of the crm resources', function (): void {
    foreach ([CounterpartyResource::class, ContactResource::class, LeadResource::class, OpportunityResource::class] as $resource) {
        expect($resource::getPages())->toHaveKeys(['index', 'create', 'edit']);
    }

    $names = array_map(
        static fn ($component): ?string => method_exists($component, 'getName') ? $component->getName() : null,
        ContactResource::form(new Filament\Schemas\Schema())->getComponents(),
    );

    expect($names)->toContain('counterparty_ids')
        ->and($names)->not->toContain('company_id');
});
