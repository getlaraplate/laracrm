<?php

declare(strict_types=1);

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Modules\Core\Enums\CoreTables;
use Modules\CRM\Casts\EntityType;
use Modules\CRM\Database\Seeders\CRMDatabaseSeeder;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Entity;
use Modules\CRM\Tests\Support\ContactRoleTaxonomy;

it('stores the role and the primary flag on the link', function (): void {
    $role = ContactRoleTaxonomy::insertMinimalId('role-purchasing');
    $contact = Contact::factory()->create();
    $counterparty = Counterparty::factory()->create();

    $counterparty->contacts()->attach($contact->getKey(), ['role_taxonomy_id' => $role, 'is_primary' => true]);

    $link = $counterparty->contacts()->first()->pivot;

    expect($link->role_taxonomy_id)->toBe($role)
        ->and($link->is_primary)->toBeTrue()
        ->and($counterparty->contacts()->first()->pivot->role->getKey())->toBe($role);
});

it('lets one contact hold a different role at each counterparty', function (): void {
    $purchasing = ContactRoleTaxonomy::insertMinimalId('role-purchasing');
    $consultant = ContactRoleTaxonomy::insertMinimalId('role-consultant', 'Consultant');
    $contact = Contact::factory()->create();
    $first = Counterparty::factory()->create();
    $second = Counterparty::factory()->create();

    $first->contacts()->attach($contact->getKey(), ['role_taxonomy_id' => $purchasing]);
    $second->contacts()->attach($contact->getKey(), ['role_taxonomy_id' => $consultant]);

    $roles = $contact->counterparties()->get()->mapWithKeys(fn (Counterparty $c): array => [$c->getKey() => $c->pivot->role_taxonomy_id]);

    expect($roles->all())->toBe([$first->getKey() => $purchasing, $second->getKey() => $consultant]);
});

it('keeps a single primary contact per counterparty by demoting the previous one', function (): void {
    $counterparty = Counterparty::factory()->create();
    $other = Counterparty::factory()->create();
    $first = Contact::factory()->create();
    $second = Contact::factory()->create();

    $counterparty->contacts()->attach($first->getKey(), ['is_primary' => true]);
    $other->contacts()->attach($first->getKey(), ['is_primary' => true]);
    $counterparty->contacts()->attach($second->getKey(), ['is_primary' => true]);

    $primaries = fn (Counterparty $c): array => $c->contacts()->wherePivot('is_primary', true)->pluck('crm_contacts.id')->all();

    expect($primaries($counterparty))->toBe([$second->getKey()])
        ->and($primaries($other))->toBe([$first->getKey()]);
});

it('promotes an existing link to primary through the pivot update', function (): void {
    $counterparty = Counterparty::factory()->create();
    $first = Contact::factory()->create();
    $second = Contact::factory()->create();
    $counterparty->contacts()->attach($first->getKey(), ['is_primary' => true]);
    $counterparty->contacts()->attach($second->getKey());

    $counterparty->contacts()->updateExistingPivot($second->getKey(), ['is_primary' => true]);

    expect($counterparty->contacts()->wherePivot('is_primary', true)->pluck('crm_contacts.id')->all())->toBe([$second->getKey()]);
});

it('refuses to delete a role that is in use', function (): void {
    $role = ContactRoleTaxonomy::insertMinimalId('role-in-use');
    $counterparty = Counterparty::factory()->create();
    $counterparty->contacts()->attach(Contact::factory()->create()->getKey(), ['role_taxonomy_id' => $role]);

    expect(fn () => DB::table(CoreTables::Taxonomies->value)->where('id', $role)->delete())->toThrow(QueryException::class);
});

it('seeds the contact role entity once', function (): void {
    Artisan::call('db:seed', ['--class' => CRMDatabaseSeeder::class, '--no-interaction' => true]);
    Artisan::call('db:seed', ['--class' => CRMDatabaseSeeder::class, '--no-interaction' => true]);

    expect(Entity::query()->withoutGlobalScopes()->where('name', 'contact_role')->count())->toBe(1)
        ->and(Entity::query()->withoutGlobalScopes()->where('name', 'contact_role')->value('type'))->toBe(EntityType::ContactRoles);
});
