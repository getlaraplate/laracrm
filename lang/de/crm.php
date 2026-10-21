<?php

declare(strict_types=1);

return [
    'entities' => [
        'counterparties' => [
            'plural' => 'Geschäftspartner',
            'singular' => 'Geschäftspartner',
        ],
        'contacts' => [
            'plural' => 'Kontakte',
            'singular' => 'Kontakt',
        ],
        'leads' => [
            'plural' => 'Leads',
            'singular' => 'Lead',
        ],
        'opportunities' => [
            'plural' => 'Verkaufschancen',
            'singular' => 'Verkaufschance',
        ],
    ],
    'fields' => [
        'new_counterparty_name' => 'Name des neuen Unternehmens',
        'new_counterparty_fiscal_country' => 'Land des neuen Unternehmens',
        'new_counterparty_vat_number' => 'USt-IdNr. des neuen Unternehmens',
        'new_counterparty_tax_id' => 'Steuernummer des neuen Unternehmens',
        'notes' => 'Notizen',
        'owner_user_id' => 'Verantwortlich',
        'stage' => 'Phase',
        'stage_taxonomy_id' => 'Phase',
        'is_customer' => 'Kunde',
        'is_supplier' => 'Lieferant',
        'vat_number' => 'USt-IdNr.',
        'tax_id' => 'Steuernummer',
        'fiscal_country' => 'Steuerland',
        'address_line' => 'Adresse',
        'postal_code' => 'Postleitzahl',
        'city' => 'Stadt',
        'province' => 'Provinz',
        'country' => 'Land',
        'einvoice_recipient_code' => 'Empfängercode',
        'einvoice_pec_email' => 'PEC-E-Mail',
        'name' => 'Name',
        'status' => 'Status',
        'kind' => 'Art',
        'parent_counterparty_id' => 'Übergeordneter Geschäftspartner',
        'counterparty_id' => 'Geschäftspartner',
        'counterparty_ids' => 'Geschäftspartner',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Lieferanten ausblenden, die keine Kunden sind',
    ],
    'navigation' => [
        'master_data' => 'CRM - Stammdaten',
        'pipeline' => 'CRM - Pipeline',
    ],
    'help' => [
        'stageTaxonomiesRequired' => 'Pipeline-Phasen benötigen Taxonomien: CRMDatabaseSeeder und dann DevCRMDatabaseSeeder ausführen.',
    ],
];
