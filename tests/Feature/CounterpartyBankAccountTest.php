<?php

declare(strict_types=1);

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Modules\Core\Models\Permission;
use Modules\Core\Support\PermissionName;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\CounterpartyBankAccount;

it('normalises iban, bic and currency on save', function (): void {
    $account = CounterpartyBankAccount::query()->create([
        'counterparty_id' => Counterparty::factory()->create()->getKey(),
        'beneficiary_name' => 'Acme S.r.l.',
        'iban' => 'it60 x054 2811 1010 0000 0123 456',
        'bic' => 'bpmoit22',
        'currency' => 'eur',
    ]);

    expect($account->iban)->toBe('IT60X0542811101000000123456')
        ->and($account->bic)->toBe('BPMOIT22')
        ->and($account->currency)->toBe('EUR')
        ->and($account->fresh()->is_active)->toBeTrue();
});

it('allows an iban once per counterparty', function (): void {
    $counterparty = Counterparty::factory()->create();
    $attributes = ['counterparty_id' => $counterparty->getKey(), 'beneficiary_name' => 'Acme', 'iban' => 'IT60X0542811101000000123456', 'currency' => 'EUR'];

    CounterpartyBankAccount::query()->create($attributes);

    expect(fn () => CounterpartyBankAccount::query()->create($attributes))->toThrow(QueryException::class);

    $other = Counterparty::factory()->create();

    expect(CounterpartyBankAccount::query()->create([...$attributes, 'counterparty_id' => $other->getKey()])->exists)->toBeTrue();
});

it('belongs to its counterparty and is removed with it', function (): void {
    $counterparty = Counterparty::factory()->create();
    $account = CounterpartyBankAccount::query()->create([
        'counterparty_id' => $counterparty->getKey(),
        'beneficiary_name' => 'Acme',
        'iban' => 'IT60X0542811101000000123456',
        'currency' => 'EUR',
    ]);

    expect($counterparty->bank_accounts()->count())->toBe(1)
        ->and($account->counterparty->is($counterparty))->toBeTrue();

    $counterparty->forceDelete();

    expect(CounterpartyBankAccount::query()->withTrashed()->count())->toBe(0);
});

it('guards bank accounts with their own crud permissions, apart from the counterparty ones', function (): void {
    Artisan::call('permission:refresh');

    $account_permission = PermissionName::forClass(CounterpartyBankAccount::class, 'select');

    expect(Permission::query()->pluck('name')->all())->toContain($account_permission)
        ->and($account_permission)->not->toBe(PermissionName::forClass(Counterparty::class, 'select'));
});
