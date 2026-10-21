<?php

declare(strict_types=1);

return [
    'entities' => [
        'contacts' => [
            'plural' => 'Contacts',
            'singular' => 'Contact',
        ],
        'counterparties' => [
            'plural' => 'Counterparties',
            'singular' => 'Counterparty',
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
        'address_line' => 'Address',
        'city' => 'City',
        'counterparty_id' => 'Counterparty',
        'counterparty_ids' => 'Counterparties',
        'country' => 'Country',
        'einvoice_pec_email' => 'PEC email',
        'einvoice_recipient_code' => 'Recipient code',
        'fiscal_country' => 'Fiscal country',
        'is_customer' => 'Customer',
        'is_supplier' => 'Supplier',
        'kind' => 'Kind',
        'name' => 'Name',
        'new_counterparty_fiscal_country' => 'New company country',
        'new_counterparty_name' => 'New company name',
        'new_counterparty_tax_id' => 'New company tax id',
        'new_counterparty_vat_number' => 'New company VAT number',
        'notes' => 'Notes',
        'owner_user_id' => 'Owner',
        'parent_counterparty_id' => 'Parent counterparty',
        'postal_code' => 'Postal code',
        'province' => 'Province',
        'stage' => 'Stage',
        'stage_taxonomy_id' => 'Stage',
        'status' => 'Status',
        'tax_id' => 'Tax code',
        'vat_number' => 'VAT number',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Hide suppliers that are not customers',
    ],
    'help' => [
        'stageTaxonomiesRequired' => 'Pipeline stages require taxonomies: run CRMDatabaseSeeder then DevCRMDatabaseSeeder so stages exist.',
    ],
    'navigation' => [
        'master_data' => 'CRM - Master data',
        'pipeline' => 'CRM - Pipeline',
    ],
];
