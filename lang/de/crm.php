<?php

declare(strict_types=1);

return [
    'entities' => [
        'contacts' => [
            'plural' => 'Kontakte',
            'singular' => 'Kontakt',
        ],
        'counterparties' => [
            'plural' => 'Geschäftspartner',
            'singular' => 'Geschäftspartner',
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
        'address_line' => 'Adresse',
        'city' => 'Stadt',
        'counterparty_id' => 'Geschäftspartner',
        'counterparty_ids' => 'Geschäftspartner',
        'country' => 'Land',
        'einvoice_pec_email' => 'PEC-E-Mail',
        'einvoice_recipient_code' => 'Empfängercode',
        'fiscal_country' => 'Steuerland',
        'is_customer' => 'Kunde',
        'is_supplier' => 'Lieferant',
        'kind' => 'Art',
        'name' => 'Name',
        'new_counterparty_fiscal_country' => 'Land des neuen Unternehmens',
        'new_counterparty_name' => 'Name des neuen Unternehmens',
        'new_counterparty_tax_id' => 'Steuernummer des neuen Unternehmens',
        'new_counterparty_vat_number' => 'USt-IdNr. des neuen Unternehmens',
        'notes' => 'Notizen',
        'owner_user_id' => 'Verantwortlich',
        'parent_counterparty_id' => 'Übergeordneter Geschäftspartner',
        'postal_code' => 'Postleitzahl',
        'province' => 'Provinz',
        'stage' => 'Phase',
        'stage_taxonomy_id' => 'Phase',
        'status' => 'Status',
        'tax_id' => 'Steuernummer',
        'vat_number' => 'USt-IdNr.',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Lieferanten ausblenden, die keine Kunden sind',
    ],
    'help' => [
        'stageTaxonomiesRequired' => 'Pipeline-Phasen benötigen Taxonomien: CRMDatabaseSeeder und dann DevCRMDatabaseSeeder ausführen.',
    ],
    'reports' => [
        'count' => 'Anzahl',
        'expectedDoc' => 'Erwartet (Beleg)',
        'expectedLocal' => 'Erwartet (Landeswährung)',
        'lost' => 'Verloren:',
        'lostCount' => 'Anzahl verloren:',
        'noPipelineData' => 'Keine Pipeline-Daten für die gewählten Filter.',
        'pipelineSummary' => 'Pipeline-Übersicht',
        'status' => 'Status',
        'total' => 'Summe',
        'totalOpportunities' => 'Verkaufschancen gesamt:',
        'totalPipeline' => 'Pipeline gesamt:',
        'won' => 'Gewonnen:',
        'wonValue' => 'Gewonnener Wert:',
    ],
    'pages' => [
        'salesPipeline' => 'Vertriebspipeline',
    ],
    'navigation' => [
        'master_data' => 'CRM - Stammdaten',
        'pipeline' => 'CRM - Pipeline',
    ],
];
