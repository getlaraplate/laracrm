<?php

declare(strict_types=1);

namespace Modules\CRM\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Overrides\Seeder;
use Modules\Core\Services\PresetVersioningService;
use Modules\CRM\Casts\EntityType;
use Modules\CRM\Models\Entity;
use Modules\CRM\Models\Preset;

/**
 * Bootstraps the CRM module: the opportunity stage entity and its default preset. The stage tree
 * itself is seeded by the dev fixtures.
 */
final class CRMDatabaseSeeder extends Seeder
{
    private const string STAGE_ENTITY = 'opportunity_stage';

    private const string DEFAULT_PRESET = 'standard';

    public function run(): void
    {
        Model::unguarded(function (): void {
            $this->defaultEntities();
        });
    }

    private function defaultEntities(): void
    {
        $this->logOperation(Entity::class);

        $entity_model = new Entity;

        $entity_model->getConnection()->transaction(function () use ($entity_model): void {
            if ($entity_model->newQuery()->withoutGlobalScopes()->where('name', self::STAGE_ENTITY)->exists()) {
                $this->command?->line('    - ' . self::STAGE_ENTITY . ' already exists');

                return;
            }

            /** @var Entity $entity */
            $entity = $this->create(Entity::class, [
                'name' => self::STAGE_ENTITY,
                'type' => EntityType::OpportunityStages,
            ]);

            /** @var Preset $preset */
            $preset = $this->create(Preset::class, ['name' => self::DEFAULT_PRESET, 'entity_id' => $entity->id]);

            resolve(PresetVersioningService::class)->createVersion($preset);

            $this->command?->line('    - ' . self::STAGE_ENTITY . ' <fg=green>created</>');
        });
    }
}
