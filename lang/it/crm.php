<?php

declare(strict_types=1);

return [
    'entities' => [
        'counterparties' => [
            'plural' => 'Controparti',
            'singular' => 'Controparte',
        ],
        'contacts' => [
            'plural' => 'Contatti',
            'singular' => 'Contatto',
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
        'new_counterparty_name' => 'Nome nuova azienda',
        'new_counterparty_fiscal_country' => 'Paese nuova azienda',
        'new_counterparty_vat_number' => 'Partita IVA nuova azienda',
        'new_counterparty_tax_id' => 'Codice fiscale nuova azienda',
        'notes' => 'Note',
        'owner_user_id' => 'Responsabile',
        'stage' => 'Fase',
        'stage_taxonomy_id' => 'Fase',
        'is_customer' => 'Cliente',
        'is_supplier' => 'Fornitore',
        'vat_number' => 'Partita IVA',
        'tax_id' => 'Codice fiscale',
        'fiscal_country' => 'Paese fiscale',
        'address_line' => 'Indirizzo',
        'postal_code' => 'CAP',
        'city' => 'Città',
        'province' => 'Provincia',
        'country' => 'Paese',
        'einvoice_recipient_code' => 'Codice destinatario',
        'einvoice_pec_email' => 'Email PEC',
        'name' => 'Nome',
        'status' => 'Stato',
        'kind' => 'Tipo',
        'parent_counterparty_id' => 'Controparte capogruppo',
        'counterparty_id' => 'Controparte',
        'counterparty_ids' => 'Controparti',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Nascondi i fornitori che non sono clienti',
    ],
    'navigation' => [
        'master_data' => 'CRM - Anagrafiche',
        'pipeline' => 'CRM - Pipeline',
    ],
    'help' => [
        'stageTaxonomiesRequired' => 'Le fasi della pipeline richiedono le tassonomie: esegui CRMDatabaseSeeder e poi DevCRMDatabaseSeeder.',
    ],
];
