<?php

declare(strict_types=1);

namespace Modules\CRM\Tests\Support;

use Modules\CRM\Models\ContactRole;

/**
 * Builds a minimal contact_role taxonomy row for CRM feature tests.
 */
final class ContactRoleTaxonomy
{
    /**
     * @param  non-empty-string  $translationSlug  Used to build a unique taxonomies_translations.slug
     */
    public static function insertMinimalId(string $translationSlug, string $name = 'Purchasing'): int
    {
        return CrmTaxonomyRow::insert('contact_role', ContactRole::class, $translationSlug, $name);
    }
}
