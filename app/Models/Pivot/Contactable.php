<?php

declare(strict_types=1);

namespace Modules\CRM\Models\Pivot;

use Modules\Core\Overrides\Pivot;
use Modules\CRM\Enums\CRMTables;
use Override;

/**
 * Pivot linking CRM counterparties and contacts.
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
    ];

    protected function casts(): array
    {
        return [
            'counterparty_id' => 'integer',
            'contact_id' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'datetime',
        ];
    }
}
