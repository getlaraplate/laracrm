<?php

declare(strict_types=1);

return [
    'entities' => [
        'counterparties' => [
            'plural' => 'Counterparties',
            'singular' => 'Counterparty',
        ],
        'contacts' => [
            'plural' => 'Contacts',
            'singular' => 'Contact',
        ],
        'leads' => [
            'plural' => 'Leads',
            'singular' => 'Lead',
        ],
        'opportunities' => [
            'plural' => 'Opportunities',
            'singular' => 'Opportunity',
        ],
    ],
    'fields' => [
        'notes' => 'Notes',
        'owner_user_id' => 'Owner',
        'stage' => 'Stage',
        'stage_taxonomy_id' => 'Stage',
        'is_customer' => 'Customer',
        'is_supplier' => 'Supplier',
        'vat_number' => 'VAT number',
        'tax_id' => 'Tax code',
        'fiscal_country' => 'Fiscal country',
        'address_line' => 'Address',
        'postal_code' => 'Postal code',
        'city' => 'City',
        'province' => 'Province',
        'country' => 'Country',
        'einvoice_recipient_code' => 'Recipient code',
        'einvoice_pec_email' => 'PEC email',
        'name' => 'Name',
        'status' => 'Status',
        'kind' => 'Kind',
        'parent_counterparty_id' => 'Parent counterparty',
        'counterparty_id' => 'Counterparty',
        'counterparty_ids' => 'Counterparties',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Hide suppliers that are not customers',
    ],
    'navigation' => [
        'master_data' => 'CRM - Master data',
        'pipeline' => 'CRM - Pipeline',
    ],
    'help' => [
        'stageTaxonomiesRequired' => 'Pipeline stages require taxonomies: run CRMDatabaseSeeder then DevCRMDatabaseSeeder so stages exist.',
    ],
];
