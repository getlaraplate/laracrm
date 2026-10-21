<?php

declare(strict_types=1);

return [
    'entities' => [
        'contacts' => [
            'plural' => 'Contatti',
            'singular' => 'Contatto',
        ],
        'counterparties' => [
            'plural' => 'Controparti',
            'singular' => 'Controparte',
        ],
        'leads' => [
            'plural' => 'Lead',
            'singular' => 'Lead',
        ],
        'opportunities' => [
            'plural' => 'Opportunità',
            'singular' => 'Opportunità',
        ],
    ],
    'fields' => [
        'address_line' => 'Indirizzo',
        'city' => 'Città',
        'counterparty_id' => 'Controparte',
        'counterparty_ids' => 'Controparti',
        'country' => 'Paese',
        'einvoice_pec_email' => 'Email PEC',
        'einvoice_recipient_code' => 'Codice destinatario',
        'fiscal_country' => 'Paese fiscale',
        'is_customer' => 'Cliente',
        'is_supplier' => 'Fornitore',
        'kind' => 'Tipo',
        'name' => 'Nome',
        'new_counterparty_fiscal_country' => 'Paese nuova azienda',
        'new_counterparty_name' => 'Nome nuova azienda',
        'new_counterparty_tax_id' => 'Codice fiscale nuova azienda',
        'new_counterparty_vat_number' => 'Partita IVA nuova azienda',
        'notes' => 'Note',
        'owner_user_id' => 'Responsabile',
        'parent_counterparty_id' => 'Controparte capogruppo',
        'postal_code' => 'CAP',
        'province' => 'Provincia',
        'stage' => 'Fase',
        'stage_taxonomy_id' => 'Fase',
        'status' => 'Stato',
        'tax_id' => 'Codice fiscale',
        'vat_number' => 'Partita IVA',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Nascondi i fornitori che non sono clienti',
    ],
    'help' => [
        'stageTaxonomiesRequired' => 'Le fasi della pipeline richiedono le tassonomie: esegui CRMDatabaseSeeder e poi DevCRMDatabaseSeeder.',
    ],
    'navigation' => [
        'master_data' => 'CRM - Anagrafiche',
        'pipeline' => 'CRM - Pipeline',
    ],
];
