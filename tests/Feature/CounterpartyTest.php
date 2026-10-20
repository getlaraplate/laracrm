<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use Modules\CRM\Casts\CounterpartyKind;
use Modules\CRM\Models\Counterparty;

it('defaults kind to company', function (): void {
    $counterparty = Counterparty::query()->create(['name' => 'Acme S.r.l.'])->refresh();

    expect($counterparty->kind)->toBe(CounterpartyKind::Company);
});

it('finds by country and vat number', function (): void {
    $match = Counterparty::factory()->create(['fiscal_country' => 'IT', 'vat_number' => 'IT01234567890']);
    Counterparty::factory()->create(['fiscal_country' => 'DE', 'vat_number' => 'IT01234567890']);

    expect(Counterparty::findByFiscalIdentity('IT', 'IT01234567890', null)?->is($match))->toBeTrue();
});

it('finds by tax id when there is no vat number', function (): void {
    $match = Counterparty::factory()->person()->create(['vat_number' => null, 'tax_id' => 'RSSMRA80A01H501U']);

    expect(Counterparty::findByFiscalIdentity(null, null, 'RSSMRA80A01H501U')?->is($match))->toBeTrue();
});

it('returns null for blank identifiers', function (): void {
    Counterparty::factory()->create(['vat_number' => null, 'tax_id' => null]);
    Counterparty::factory()->create(['vat_number' => '', 'tax_id' => '']);

    expect(Counterparty::findByFiscalIdentity(null, null, null))->toBeNull()
        ->and(Counterparty::findByFiscalIdentity('IT', '', ''))->toBeNull();
});

it('refuses to be its own ancestor', function (): void {
    $root = Counterparty::factory()->create();
    $child = Counterparty::factory()->create(['parent_counterparty_id' => $root->getKey()]);

    $root->parent_counterparty_id = $child->getKey();

    expect(fn () => $root->save())->toThrow(ValidationException::class);

    $child->parent_counterparty_id = $child->getKey();

    expect(fn () => $child->save())->toThrow(ValidationException::class);
});
