<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Enums\CoreTables;
use Modules\Core\Enums\VersionStrategy;
use Modules\Core\Overrides\Model;
use Modules\CRM\Enums\CRMTables;
use Modules\CRM\Casts\OpportunityStatus;
use Override;

/**
 * CRM opportunity: a qualified deal with a {@see Counterparty} on a pipeline {@see OpportunityStage}.
 */
final class Opportunity extends Model
{
    /**
     * @var string
     */
    #[Override]
    protected $table = CRMTables::Opportunities->value;

    protected VersionStrategy $versionStrategy = VersionStrategy::DIFF;

    /**
     * The attributes that are mass assignable.
     */
    #[Override]
    protected $fillable = [
        'lead_id',
        'counterparty_id',
        'stage_taxonomy_id',
        'name',
        'status',
        'expected_close_date',
        'expected_value_doc',
        'expected_currency_doc',
        'expected_value_local',
        'expected_currency_local',
        'expected_fx_rate',
        'probability',
        'won_at',
        'lost_at',
        'lost_reason',
    ];

    /**
     * @return BelongsTo<Lead, $this>
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * @return BelongsTo<Counterparty, $this>
     */
    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class)->withTrashed();
    }

    /**
     * @return BelongsTo<OpportunityStage, $this>
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(OpportunityStage::class, 'stage_taxonomy_id');
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function getRules(): array
    {
        $rules = parent::getRules();
        $rules['create'] = array_merge($rules['create'], [
            'lead_id' => ['nullable', 'integer', 'exists:' . CRMTables::Leads->value . ',id'],
            'counterparty_id' => ['required', 'integer', 'exists:' . CRMTables::Counterparties->value . ',id'],
            'stage_taxonomy_id' => ['required', 'integer', 'exists:' . CoreTables::Taxonomies->value . ',id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', OpportunityStatus::validationRule()],
            'expected_close_date' => ['nullable', 'date'],
            'expected_value_doc' => ['nullable', 'numeric', 'min:0'],
            'expected_currency_doc' => ['required', 'string', 'size:3'],
            'expected_value_local' => ['nullable', 'numeric', 'min:0'],
            'expected_currency_local' => ['required', 'string', 'size:3'],
            'expected_fx_rate' => ['required', 'numeric', 'min:0'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'won_at' => ['nullable', 'date'],
            'lost_at' => ['nullable', 'date'],
            'lost_reason' => ['nullable', 'string'],
        ]);
        $rules['update'] = array_merge($rules['update'], [
            'lead_id' => ['nullable', 'integer', 'exists:' . CRMTables::Leads->value . ',id'],
            'counterparty_id' => ['sometimes', 'integer', 'exists:' . CRMTables::Counterparties->value . ',id'],
            'stage_taxonomy_id' => ['sometimes', 'integer', 'exists:' . CoreTables::Taxonomies->value . ',id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', OpportunityStatus::validationRule()],
            'expected_close_date' => ['nullable', 'date'],
            'expected_value_doc' => ['nullable', 'numeric', 'min:0'],
            'expected_currency_doc' => ['sometimes', 'string', 'size:3'],
            'expected_value_local' => ['nullable', 'numeric', 'min:0'],
            'expected_currency_local' => ['sometimes', 'string', 'size:3'],
            'expected_fx_rate' => ['sometimes', 'numeric', 'min:0'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'won_at' => ['nullable', 'date'],
            'lost_at' => ['nullable', 'date'],
            'lost_reason' => ['nullable', 'string'],
        ]);

        return $rules;
    }

    protected function casts(): array
    {
        return [
            'status' => OpportunityStatus::class,
            'expected_close_date' => 'immutable_date',
            'expected_value_doc' => 'decimal:4',
            'expected_value_local' => 'decimal:4',
            'expected_fx_rate' => 'decimal:8',
            'probability' => 'integer',
            'won_at' => 'immutable_datetime',
            'lost_at' => 'immutable_datetime',
        ];
    }
}
