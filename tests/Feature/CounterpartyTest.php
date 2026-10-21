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

it('normalises fiscal identifiers so that case and spaces do not create a second subject', function (): void {
    $stored = Counterparty::query()->create(['name' => 'Acme', 'fiscal_country' => 'it', 'vat_number' => ' it 0123 4567 890 ', 'tax_id' => 'rssmra80a01h501u']);

    expect($stored->fresh()->vat_number)->toBe('IT01234567890')
        ->and($stored->fresh()->tax_id)->toBe('RSSMRA80A01H501U')
        ->and($stored->fresh()->fiscal_country)->toBe('IT')
        ->and(Counterparty::findByFiscalIdentity('it', 'it 01234567890', null)?->is($stored))->toBeTrue()
        ->and(Counterparty::findByFiscalIdentity(null, null, 'rssmra80a01h501u')?->is($stored))->toBeTrue();
});

it('finds a soft deleted counterparty by its fiscal identity', function (): void {
    $trashed = Counterparty::factory()->create(['fiscal_country' => 'IT', 'vat_number' => 'IT07777777777']);
    $trashed->delete();

    expect(Counterparty::findByFiscalIdentity('IT', 'IT07777777777', null)?->is($trashed))->toBeTrue();
});
