<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Modules\Core\Contracts\IDynamicEntityTypable;
use Modules\Core\Models\Taxonomy;
use Modules\CRM\Casts\EntityType;
use Modules\CRM\Models\Pivot\Presettable;
use Override;

/**
 * Role a contact holds at a counterparty; rows live in `taxonomies` with {@see EntityType::ContactRoles}.
 */
final class ContactRole extends Taxonomy
{
    #[Override]
    public static function getEntityModelClass(): string
    {
        return Entity::class;
    }

    #[Override]
    public static function getPresettableClass(): string
    {
        return Presettable::class;
    }

    #[Override]
    protected static function getEntityType(): IDynamicEntityTypable
    {
        return EntityType::ContactRoles;
    }
}
