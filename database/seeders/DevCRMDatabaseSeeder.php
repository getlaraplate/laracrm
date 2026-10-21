<?php

declare(strict_types=1);

namespace Modules\CRM\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Core\Models\Translations\TaxonomyTranslation;
use Modules\Core\Overrides\Seeder;
use Modules\CRM\Casts\EntityType;
use Modules\CRM\Casts\LeadStatus;
use Modules\CRM\Casts\OpportunityStatus;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Entity;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Opportunity;
use Modules\CRM\Models\OpportunityStage;
use Modules\CRM\Models\Pivot\Presettable;

/**
 * Dev fixture: the pipeline stages plus a few demo prospects (counterparties without a party), a lead
 * and an opportunity. Every row is keyed on a stable identifier so a second run changes nothing.
 */
final class DevCRMDatabaseSeeder extends Seeder
{
    private const string DEMO_PROSPECT_TAX_ID = '55566677788';

    public function run(): void
    {
        Model::unguarded(function (): void {
            (new Entity)->getConnection()->transaction(function (): void {
                $this->seedOpportunityStages();
                $this->seedDemoPipeline();
            });
        });
    }

    private function seedOpportunityStages(): void
    {
        $this->logOperation(OpportunityStage::class);

        /**
         * @var array<int, array{slug: string, it: string, en: string}> $stages
         */
        $stages = [
            ['slug' => 'opp-new', 'it' => 'Nuovo', 'en' => 'New'],
            ['slug' => 'opp-qualified', 'it' => 'Qualificata', 'en' => 'Qualified'],
            ['slug' => 'opp-proposal', 'it' => 'Proposta', 'en' => 'Proposal'],
            ['slug' => 'opp-won', 'it' => 'Vinta', 'en' => 'Won'],
            ['slug' => 'opp-lost', 'it' => 'Persa', 'en' => 'Lost'],
        ];

        $entity_id = Entity::query()
            ->withoutGlobalScopes()
            ->where('name', 'opportunity_stage')
            ->where('type', EntityType::OpportunityStages->value)
            ->value('id');

        if (! $entity_id) {
            $this->command?->warn('Skipping opportunity stages: Entity "opportunity_stage" not found. Run CRMDatabaseSeeder first.');

            return;
        }

        $presettable_id = Presettable::query()
            ->withoutEagerLoads()
            ->where('entity_id', $entity_id)
            ->whereNull('deleted_at')
            ->latest('id')
            ->value('id');

        if (! $presettable_id) {
            $this->command?->warn('Skipping opportunity stages: no active presettable for entity.');

            return;
        }

        $existing_slugs = TaxonomyTranslation::query()
            ->whereIn('slug', array_column($stages, 'slug'))
            ->pluck('slug')
            ->all();

        foreach ($stages as $node) {
            if (in_array($node['slug'], $existing_slugs, true)) {
                $this->command?->line("    - opportunity stage {$node['slug']} already exists");

                continue;
            }

            $stage = OpportunityStage::query()->forceCreate([
                'parent_id' => null,
                'presettable_id' => $presettable_id,
                'entity_id' => $entity_id,
            ]);

            foreach (['it', 'en'] as $locale) {
                TaxonomyTranslation::query()->forceCreate([
                    'taxonomy_id' => $stage->id,
                    'locale' => $locale,
                    'name' => $node[$locale],
                    'slug' => Str::slug($node['slug']),
                    'components' => [],
                ]);
            }

            $this->command?->line("    - opportunity stage {$node['slug']} <fg=green>created</>");
        }
    }

    private function seedDemoPipeline(): void
    {
        $this->logOperation(Opportunity::class);

        $prospect = Counterparty::query()->firstOrCreate(
            ['tax_id' => self::DEMO_PROSPECT_TAX_ID],
            ['name' => 'Prospect Demo S.r.l.', 'fiscal_country' => 'IT', 'city' => 'Bologna', 'country' => 'IT'],
        );

        $lead = Lead::query()->firstOrCreate(
            ['counterparty_id' => $prospect->getKey(), 'title' => 'Richiesta di preventivo da fiera'],
            ['status' => LeadStatus::Qualified, 'source' => 'trade-fair'],
        );

        $stage_id = TaxonomyTranslation::query()
            ->where('slug', Str::slug('opp-proposal'))
            ->value('taxonomy_id');

        if ($stage_id === null) {
            $this->command?->warn('Skipping the demo opportunity: the pipeline stages are missing.');

            return;
        }

        Opportunity::query()->firstOrCreate(
            ['counterparty_id' => $prospect->getKey(), 'name' => 'Fornitura annuale Prospect Demo'],
            [
                'lead_id' => $lead->getKey(),
                'stage_taxonomy_id' => $stage_id,
                'status' => OpportunityStatus::Open,
                'expected_value_doc' => 12000,
                'expected_currency_doc' => 'EUR',
                'expected_value_local' => 12000,
                'expected_currency_local' => 'EUR',
                'expected_fx_rate' => 1,
                'probability' => 50,
            ],
        );

        $this->command?->line('    - demo prospect, lead and opportunity ensured');
    }
}
