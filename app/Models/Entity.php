<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Modules\Core\Models\Entity as CoreEntity;
use Modules\CRM\Casts\EntityType;
use Override;

final class Entity extends CoreEntity
{
    #[Override]
    protected static function getEntityTypeEnumClass(): string
    {
        return EntityType::class;
    }
}
