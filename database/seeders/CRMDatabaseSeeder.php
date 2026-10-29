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
 * Bootstraps the CRM module: the opportunity stage and contact role entities and their default
 * presets. The stage tree itself is seeded by the dev fixtures.
 */
final class CRMDatabaseSeeder extends Seeder
{
    /**
     * Taxonomy entities owned by the CRM, by entity name.
     *
     * @var array<string, EntityType>
     */
    private const array ENTITIES = [
        'opportunity_stage' => EntityType::OpportunityStages,
        'contact_role' => EntityType::ContactRoles,
    ];

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

        $created = $entity_model->getConnection()->transaction(function () use ($entity_model): int {
            $created = 0;

            foreach (self::ENTITIES as $name => $type) {
                if ($entity_model->newQuery()->withoutGlobalScopes()->where('name', $name)->exists()) {
                    continue;
                }

                /** @var Entity $entity */
                $entity = $this->create(Entity::class, ['name' => $name, 'type' => $type]);

                /** @var Preset $preset */
                $preset = $this->create(Preset::class, ['name' => self::DEFAULT_PRESET, 'entity_id' => $entity->id]);

                resolve(PresetVersioningService::class)->createVersion($preset);

                $created++;
            }

            return $created;
        });

        $this->logOutcome($created, 0, count(self::ENTITIES) - $created);
    }
}
