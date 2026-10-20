<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Enums\CoreTables;
use Modules\Core\Models\Preset as CorePreset;
use Modules\CRM\Casts\EntityType;
use Override;

/**
 * CRM preset model; behaviour lives in Core. The dynamic rows a preset describes are the pipeline stages.
 */
final class Preset extends CorePreset
{
    #[Override]
    protected static function getRelatedModelClass(): string
    {
        return OpportunityStage::class;
    }

    #[Override]
    protected function newBaseQueryBuilder(): Builder
    {
        return parent::newBaseQueryBuilder()->whereExists(function (Builder $query): void {
            $entities_table = CoreTables::Entities->value;
            $query->select(DB::raw('1'))
                ->from($entities_table)
                ->whereColumn("{$entities_table}.id", CoreTables::Presets->value . '.entity_id')
                ->whereIn("{$entities_table}.type", EntityType::values());
        });
    }
}
