<?php

declare(strict_types=1);

namespace Modules\CRM\Casts;

use Modules\Core\Contracts\IDynamicEntityTypable;

/**
 * Selects which CRM-owned taxonomy tree rows in `taxonomies` belong to.
 */
enum EntityType: string implements IDynamicEntityTypable
{
    /**
     * CRM pipeline stages ({@see \Modules\CRM\Models\OpportunityStage} / `taxonomies`) for opportunities.
     */
    case OpportunityStages = 'opportunity_stages';

    /**
     * Roles a contact holds at a counterparty ({@see \Modules\CRM\Models\ContactRole} / `taxonomies`), for example purchasing.
     */
    case ContactRoles = 'contact_roles';

    /**
     * Get all values as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if value is valid.
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }

    /**
     * Get validation rules for Laravel.
     */
    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }

    public function toScalar(): string
    {
        return $this->value;
    }
}
