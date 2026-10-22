<?php

declare(strict_types=1);

return [
    'actions' => [
        'convert' => 'In Opportunity umwandeln',
    ],
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
        'first_name' => 'Vorname',
        'fiscal_country' => 'Steuerland',
        'is_customer' => 'Kunde',
        'is_primary' => 'Hauptkontakt',
        'is_supplier' => 'Lieferant',
        'kind' => 'Art',
        'last_name' => 'Nachname',
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
        'role_taxonomy_id' => 'Rolle',
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
        'personNameDerived' => 'Wird aus Nachname und Vorname gebildet, wenn einer davon ausgefüllt ist.',
        'stageTaxonomiesRequired' => 'Pipeline-Phasen benötigen Taxonomien: CRMDatabaseSeeder und dann DevCRMDatabaseSeeder ausführen.',
    ],
    'messages' => [
        'leadConverted' => 'Lead in eine Opportunity umgewandelt.',
    ],
    'navigation' => [
        'master_data' => 'CRM - Stammdaten',
        'pipeline' => 'CRM - Pipeline',
    ],
    'pages' => [
        'salesPipeline' => 'Vertriebspipeline',
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
];
