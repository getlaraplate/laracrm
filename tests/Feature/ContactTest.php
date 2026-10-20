<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Modules\CRM\Enums\CRMTables;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Pivot\Contactable;

it('links a contact to several counterparties', function (): void {
    $contact = Contact::factory()->create();
    $first = Counterparty::factory()->create();
    $second = Counterparty::factory()->create();

    $first->contacts()->attach($contact->getKey());
    $second->contacts()->attach($contact->getKey());

    $linked = $contact->counterparties()->get();

    expect($linked->pluck('id')->all())->toEqualCanonicalizing([$first->getKey(), $second->getKey()])
        ->and($linked->first()->pivot)->toBeInstanceOf(Contactable::class)
        ->and($linked->first()->pivot->getTable())->toBe(CRMTables::Contactables->value);
});

it('hides the user link from serialisation and exposes whether the contact can log in', function (): void {
    $user = user_class()::factory()->create();
    $contact = Contact::factory()->create(['user_id' => $user->getKey()]);

    expect($contact->toArray())->not->toHaveKey('user_id')
        ->and($contact->fresh()->user?->is($user))->toBeTrue()
        ->and(Contact::factory()->create()->can_login)->toBeFalse();
});

it('removes the link rows when a counterparty is hard deleted', function (): void {
    $contact = Contact::factory()->create();
    $counterparty = Counterparty::factory()->create();
    $counterparty->contacts()->attach($contact->getKey());

    $counterparty->forceDelete();

    expect(DB::table(CRMTables::Contactables->value)->count())->toBe(0)
        ->and(Contact::query()->whereKey($contact->getKey())->exists())->toBeTrue();
});
