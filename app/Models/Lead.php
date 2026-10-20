<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Enums\CoreTables;
use Modules\Core\Enums\VersionStrategy;
use Modules\Core\Models\User;
use Modules\Core\Overrides\Model;
use Modules\CRM\Enums\CRMTables;
use Modules\CRM\Casts\LeadStatus;
use Override;

/**
 * CRM lead: an early-stage commercial approach to a {@see Counterparty}, before a formal {@see Opportunity}.
 */
final class Lead extends Model
{
    /**
     * @var string
     */
    #[Override]
    protected $table = CRMTables::Leads->value;

    protected VersionStrategy $versionStrategy = VersionStrategy::DIFF;

    /**
     * The attributes that are mass assignable.
     */
    #[Override]
    protected $fillable = [
        'counterparty_id',
        'contact_id',
        'title',
        'source',
        'status',
        'owner_user_id',
        'notes',
        'converted_at',
    ];

    /**
     * @return BelongsTo<Counterparty, $this>
     */
    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class)->withTrashed();
    }

    /**
     * @return BelongsTo<Contact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(user_class(), 'owner_user_id');
    }

    /**
     * @return HasMany<Opportunity, $this>
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function getRules(): array
    {
        $rules = parent::getRules();
        $rules['create'] = array_merge($rules['create'], [
            'counterparty_id' => ['nullable', 'integer', 'exists:' . CRMTables::Counterparties->value . ',id'],
            'contact_id' => ['nullable', 'integer', 'exists:' . CRMTables::Contacts->value . ',id'],
            'title' => ['required', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:128'],
            'status' => ['required', 'string', LeadStatus::validationRule()],
            'owner_user_id' => ['nullable', 'integer', 'exists:' . CoreTables::Users->value . ',id'],
            'notes' => ['nullable', 'string'],
            'converted_at' => ['nullable', 'date'],
        ]);
        $rules['update'] = array_merge($rules['update'], [
            'counterparty_id' => ['nullable', 'integer', 'exists:' . CRMTables::Counterparties->value . ',id'],
            'contact_id' => ['nullable', 'integer', 'exists:' . CRMTables::Contacts->value . ',id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:128'],
            'status' => ['sometimes', 'string', LeadStatus::validationRule()],
            'owner_user_id' => ['nullable', 'integer', 'exists:' . CoreTables::Users->value . ',id'],
            'notes' => ['nullable', 'string'],
            'converted_at' => ['nullable', 'date'],
        ]);

        return $rules;
    }

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'converted_at' => 'immutable_datetime',
        ];
    }
}
