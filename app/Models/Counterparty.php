<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use Modules\Core\Overrides\Model;
use Modules\CRM\Casts\CounterpartyKind;
use Modules\CRM\Database\Factories\CounterpartyFactory;
use Modules\CRM\Enums\CRMTables;
use Modules\CRM\Models\Pivot\Contactable;
use Override;

/**
 * The subject of every commercial relationship: a company or a natural person, shared by all the
 * companies of the installation. Legal identity lives here; the relationship with one company of
 * the group (flags, price lists, bank mandates) lives on the ERP `Party`.
 */
final class Counterparty extends Model
{
    /**
     * @var string
     */
    #[Override]
    protected $table = CRMTables::Counterparties->value;

    /**
     * The aggregate flags `is_customer` and `is_supplier` are written by the ERP and are not mass assignable.
     */
    #[Override]
    protected $fillable = [
        'name',
        'kind',
        'tax_id',
        'vat_number',
        'fiscal_country',
        'address_line',
        'postal_code',
        'city',
        'province',
        'country',
        'einvoice_recipient_code',
        'einvoice_pec_email',
        'parent_counterparty_id',
    ];

    /**
     * Match an existing counterparty by fiscal identity: country plus VAT number first, then tax id.
     * Blank identifiers never match, so two subjects without any identifier stay distinct.
     */
    public static function findByFiscalIdentity(?string $fiscalCountry, ?string $vatNumber, ?string $taxId): ?self
    {
        $vatNumber = self::normalizeIdentifier($vatNumber);
        $taxId = self::normalizeIdentifier($taxId);
        $fiscalCountry = self::normalizeIdentifier($fiscalCountry);

        if ($vatNumber !== null) {
            $match = self::query()
                ->withTrashed()
                ->where('vat_number', $vatNumber)
                ->when($fiscalCountry !== null, static fn ($query) => $query->where('fiscal_country', $fiscalCountry))
                ->orderBy('id')
                ->first();

            if ($match instanceof self) {
                return $match;
            }
        }

        if ($taxId !== null) {
            return self::query()->withTrashed()->where('tax_id', $taxId)->orderBy('id')->first();
        }

        return null;
    }

    /**
     * The counterparty a new commercial approach is about: the one with the same fiscal identity, else the one
     * with the same name, else a new one. A trashed match is restored. Stored attributes of a match are never
     * overwritten.
     *
     * @param  array<string, mixed>  $attributes  At least `name`, optionally `fiscal_country`, `vat_number`, `tax_id`.
     */
    public static function attachOrCreate(array $attributes): self
    {
        $match = self::findByFiscalIdentity(
            isset($attributes['fiscal_country']) ? (string) $attributes['fiscal_country'] : null,
            isset($attributes['vat_number']) ? (string) $attributes['vat_number'] : null,
            isset($attributes['tax_id']) ? (string) $attributes['tax_id'] : null,
        );

        $name = self::blankToNull(isset($attributes['name']) ? (string) $attributes['name'] : null);

        if (! $match instanceof self && $name !== null) {
            $match = self::query()->withTrashed()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->orderBy('id')->first();
        }

        if ($match instanceof self) {
            if ($match->trashed()) {
                $match->restore();
            }

            return $match;
        }

        return self::query()->create($attributes);
    }

    /**
     * Fiscal identifiers are matched case-insensitively and without spaces, so `it 0123` and `IT0123` are one subject.
     */
    public static function normalizeIdentifier(?string $value): ?string
    {
        $value = $value === null ? null : mb_strtoupper((string) preg_replace('/\s+/u', '', $value));

        return $value === null || $value === '' ? null : $value;
    }

    /**
     * @return BelongsTo<self, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_counterparty_id');
    }

    /**
     * @return HasMany<self, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_counterparty_id');
    }

    /**
     * @return HasMany<CounterpartyBankAccount, $this>
     */
    public function bank_accounts(): HasMany
    {
        return $this->hasMany(CounterpartyBankAccount::class);
    }

    /**
     * @return HasMany<Lead, $this>
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * @return HasMany<Opportunity, $this>
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    /**
     * @return BelongsToMany<Contact, $this>
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, CRMTables::Contactables->value, 'counterparty_id', 'contact_id')
            ->using(Contactable::class)
            ->withTimestamps();
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function getRules(): array
    {
        $rules = parent::getRules();
        $identity = [
            'tax_id' => ['nullable', 'string', 'max:32'],
            'vat_number' => ['nullable', 'string', 'max:32'],
            'fiscal_country' => ['nullable', 'string', 'size:2'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:16'],
            'city' => ['nullable', 'string', 'max:128'],
            'province' => ['nullable', 'string', 'max:8'],
            'country' => ['nullable', 'string', 'size:2'],
            'einvoice_recipient_code' => ['nullable', 'string', 'max:7'],
            'einvoice_pec_email' => ['nullable', 'email', 'max:255'],
            'parent_counterparty_id' => ['nullable', 'integer', 'exists:' . CRMTables::Counterparties->value . ',id'],
        ];
        $rules['create'] = array_merge($rules['create'], $identity, [
            'name' => ['required', 'string', 'max:255'],
            'kind' => ['sometimes', 'string', CounterpartyKind::validationRule()],
        ]);
        $rules['update'] = array_merge($rules['update'], $identity, [
            'name' => ['sometimes', 'string', 'max:255'],
            'kind' => ['sometimes', 'string', CounterpartyKind::validationRule()],
        ]);

        return $rules;
    }

    /**
     * @return Factory<self>
     */
    #[Override]
    protected static function newFactory(): Factory
    {
        return CounterpartyFactory::new();
    }

    #[Override]
    protected static function booted(): void
    {
        self::saving(static function (self $counterparty): void {
            foreach (['vat_number', 'tax_id', 'fiscal_country'] as $attribute) {
                if ($counterparty->isDirty($attribute)) {
                    $counterparty->{$attribute} = self::normalizeIdentifier($counterparty->{$attribute});
                }
            }

            if ($counterparty->parent_counterparty_id === null || ! $counterparty->isDirty('parent_counterparty_id')) {
                return;
            }

            if ($counterparty->exists && $counterparty->wouldBecomeItsOwnAncestor()) {
                throw ValidationException::withMessages([
                    'parent_counterparty_id' => ['A counterparty cannot be its own ancestor.'],
                ]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'kind' => CounterpartyKind::class,
            'is_customer' => 'boolean',
            'is_supplier' => 'boolean',
        ];
    }

    private static function blankToNull(?string $value): ?string
    {
        $value = $value === null ? null : mb_trim($value);

        return $value === null || $value === '' ? null : $value;
    }

    private function wouldBecomeItsOwnAncestor(): bool
    {
        $own_id = $this->getKey();
        $cursor = (int) $this->parent_counterparty_id;
        $visited = [];

        while ($cursor !== 0 && ! in_array($cursor, $visited, true)) {
            if ($cursor === $own_id) {
                return true;
            }

            $visited[] = $cursor;
            $cursor = (int) $this->newQuery()->withTrashed()->whereKey($cursor)->value('parent_counterparty_id');
        }

        return false;
    }
}
