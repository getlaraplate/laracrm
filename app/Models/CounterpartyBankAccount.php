<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Contracts\IsPartOfParent;
use Modules\Core\Overrides\Model;
use Modules\CRM\Enums\CRMTables;
use Override;

/**
 * A bank account of a counterparty: the IBAN belongs to the subject, so it is stored once however many
 * companies of the group deal with it. What differs per company (the default choice, the direct debit
 * mandate) lives on the ERP `PartyBankAccount` that points here.
 */
final class CounterpartyBankAccount extends Model implements IsPartOfParent
{
    /**
     * @var string
     */
    #[Override]
    protected $table = CRMTables::CounterpartyBankAccounts->value;

    #[Override]
    protected $fillable = [
        'counterparty_id',
        'beneficiary_name',
        'iban',
        'bic',
        'currency',
        'is_active',
    ];

    public static function normalizeIban(string $iban): string
    {
        return mb_strtoupper(str_replace(' ', '', $iban));
    }

    /**
     * The relation to the record this one only exists inside, whose visibility it inherits.
     */
    #[Override]
    public function parentRelation(): string
    {
        return 'counterparty';
    }

    /**
     * @return BelongsTo<Counterparty, $this>
     */
    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class)->withTrashed();
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function getRules(): array
    {
        $rules = parent::getRules();
        $rules['create'] = array_merge($rules['create'], [
            'counterparty_id' => ['required', 'integer', 'exists:' . CRMTables::Counterparties->value . ',id'],
            'beneficiary_name' => ['required', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:34'],
            'bic' => ['nullable', 'string', 'max:11'],
            'currency' => ['required', 'string', 'size:3'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $rules['update'] = array_merge($rules['update'], [
            'beneficiary_name' => ['sometimes', 'string', 'max:255'],
            'iban' => ['sometimes', 'string', 'max:34'],
            'bic' => ['nullable', 'string', 'max:11'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return $rules;
    }

    #[Override]
    protected static function booted(): void
    {
        self::saving(static function (self $bank_account): void {
            $bank_account->iban = self::normalizeIban((string) $bank_account->iban);
            $bank_account->bic = $bank_account->bic !== null
                ? mb_strtoupper(str_replace(' ', '', (string) $bank_account->bic))
                : null;
            $bank_account->currency = mb_strtoupper((string) $bank_account->currency);
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
