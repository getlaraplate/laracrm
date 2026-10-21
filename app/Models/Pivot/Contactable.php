<?php

declare(strict_types=1);

namespace Modules\CRM\Models\Pivot;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Overrides\Pivot;
use Modules\CRM\Enums\CRMTables;
use Modules\CRM\Models\ContactRole;
use Override;

/**
 * Pivot linking CRM counterparties and contacts, with the role the contact holds there and whether
 * it is the main contact. A counterparty has at most one main contact: making another one primary
 * demotes the previous.
 */
final class Contactable extends Pivot
{
    #[Override]
    public $incrementing = false;

    #[Override]
    public $timestamps = true;

    /**
     * @var string
     */
    #[Override]
    protected $table = CRMTables::Contactables->value;

    #[Override]
    protected $fillable = [
        'counterparty_id',
        'contact_id',
        'role_taxonomy_id',
        'is_primary',
    ];

    /**
     * @return BelongsTo<ContactRole, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(ContactRole::class, 'role_taxonomy_id');
    }

    protected static function booted(): void
    {
        self::saving(static function (self $link): void {
            if (! $link->is_primary || ! $link->isDirty('is_primary') && $link->exists) {
                return;
            }

            $link->newQuery()
                ->where('counterparty_id', $link->counterparty_id)
                ->where('contact_id', '!=', $link->contact_id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        });
    }

    protected function casts(): array
    {
        return [
            'counterparty_id' => 'integer',
            'contact_id' => 'integer',
            'role_taxonomy_id' => 'integer',
            'is_primary' => 'boolean',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'datetime',
        ];
    }
}
