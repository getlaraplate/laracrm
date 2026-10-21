<?php

declare(strict_types=1);

namespace Modules\CRM\Tests\Support;

use Modules\CRM\Models\OpportunityStage;

/**
 * Builds a minimal opportunity_stage taxonomy row for CRM feature tests.
 */
final class OpportunityStageTaxonomy
{
    /**
     * @param  non-empty-string  $translationSlug  Used to build a unique taxonomies_translations.slug
     */
    public static function insertMinimalId(string $translationSlug): int
    {
        return CrmTaxonomyRow::insert('opportunity_stage', OpportunityStage::class, $translationSlug);
    }
}
