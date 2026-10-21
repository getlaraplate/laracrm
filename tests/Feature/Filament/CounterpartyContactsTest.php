<?php

declare(strict_types=1);

use App\Models\User;
use Livewire\Livewire;
use Modules\Core\Models\Role;
use Modules\CRM\Filament\Resources\Counterparties\Pages\EditCounterparty;
use Modules\CRM\Filament\Resources\Counterparties\RelationManagers\ContactsRelationManager;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Tests\Support\ContactRoleTaxonomy;

function contactsManagerFor(Counterparty $counterparty): \Livewire\Features\SupportTesting\Testable
{
    $admin = User::factory()->create(['email_verified_at' => now(), 'password' => 'Aa1!FilamentAdminPass']);
    $admin->roles()->attach(Role::factory()->create(['name' => 'superadmin', 'guard_name' => 'web']));
    test()->actingAs($admin, 'admin');

    return Livewire::test(ContactsRelationManager::class, [
        'ownerRecord' => $counterparty,
        'pageClass' => EditCounterparty::class,
    ])->loadTable();
}

it('lists the contacts of a counterparty with their role and primary flag', function (): void {
    $role = ContactRoleTaxonomy::insertMinimalId('role-listed');
    $counterparty = Counterparty::factory()->create();
    $contact = Contact::factory()->create();
    $counterparty->contacts()->attach($contact->getKey(), ['role_taxonomy_id' => $role, 'is_primary' => true]);

    contactsManagerFor($counterparty)
        ->assertCanSeeTableRecords([$contact])
        ->assertTableColumnStateSet('pivot.is_primary', true, $contact);
});

it('edits the role and the primary flag of a link from the counterparty', function (): void {
    $role = ContactRoleTaxonomy::insertMinimalId('role-edited');
    $counterparty = Counterparty::factory()->create();
    $contact = Contact::factory()->create();
    $counterparty->contacts()->attach($contact->getKey());

    contactsManagerFor($counterparty)
        ->callTableAction('edit', $contact, ['role_taxonomy_id' => $role, 'is_primary' => true])
        ->assertHasNoTableActionErrors();

    $link = $counterparty->contacts()->first()->pivot;

    expect($link->role_taxonomy_id)->toBe($role)
        ->and($link->is_primary)->toBeTrue();
});
