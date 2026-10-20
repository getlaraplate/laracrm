<?php

declare(strict_types=1);

namespace Modules\CRM\Models\Pivot;

use Modules\Core\Models\Pivot\Presettable as CorePresettable;
use Modules\CRM\Models\Entity;
use Modules\CRM\Models\Preset;
use Override;

final class Presettable extends CorePresettable
{
    #[Override]
    protected function presetModelClass(): string
    {
        return Preset::class;
    }

    #[Override]
    protected function entityModelClass(): string
    {
        return Entity::class;
    }
}
