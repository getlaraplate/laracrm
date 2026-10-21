<?php

declare(strict_types=1);

use App\Models\User;
use Livewire\Livewire;
use Modules\Core\Models\Role;
use Modules\CRM\Filament\Resources\Counterparties\Pages\CreateCounterparty;
use Modules\CRM\Models\Counterparty;

function signInAsCrmAdmin(): void
{
    $admin = User::factory()->create(['email_verified_at' => now(), 'password' => 'Aa1!FilamentAdminPass']);
    $admin->roles()->attach(Role::factory()->create(['name' => 'superadmin', 'guard_name' => 'web']));
    test()->actingAs($admin, 'admin');
}

it('creates a person from the first and last name without typing the display name', function (): void {
    signInAsCrmAdmin();

    Livewire::test(CreateCounterparty::class)
        ->fillForm(['kind' => 'person', 'first_name' => 'Mario', 'last_name' => 'Rossi', 'tax_id' => 'RSSMRA80A01H501U'])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Counterparty::query()->where('tax_id', 'RSSMRA80A01H501U')->value('name'))->toBe('Rossi Mario');
});

it('still requires the name of a company', function (): void {
    signInAsCrmAdmin();

    Livewire::test(CreateCounterparty::class)
        ->fillForm(['kind' => 'company', 'name' => null])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});
