<?php

declare(strict_types=1);

return [
    'actions' => [
        'convert' => 'Converti in opportunità',
    ],
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
        'first_name' => 'Nome proprio',
        'fiscal_country' => 'Paese fiscale',
        'is_customer' => 'Cliente',
        'is_primary' => 'Referente principale',
        'is_supplier' => 'Fornitore',
        'kind' => 'Tipo',
        'last_name' => 'Cognome',
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
        'role_taxonomy_id' => 'Ruolo',
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
        'personNameDerived' => 'Derivato come "cognome nome" quando uno dei due è compilato.',
        'stageTaxonomiesRequired' => 'Le fasi della pipeline richiedono le tassonomie: esegui CRMDatabaseSeeder e poi DevCRMDatabaseSeeder.',
    ],
    'messages' => [
        'leadConverted' => 'Lead convertito in opportunità.',
    ],
    'navigation' => [
        'master_data' => 'CRM - Anagrafiche',
        'pipeline' => 'CRM - Pipeline',
    ],
    'pages' => [
        'salesPipeline' => 'Pipeline commerciale',
    ],
    'reports' => [
        'count' => 'Numero',
        'expectedDoc' => 'Previsto (documento)',
        'expectedLocal' => 'Previsto (valuta locale)',
        'lost' => 'Perse:',
        'lostCount' => 'Numero perse:',
        'noPipelineData' => 'Nessun dato di pipeline per i filtri selezionati.',
        'pipelineSummary' => 'Riepilogo della pipeline',
        'status' => 'Stato',
        'total' => 'Totale',
        'totalOpportunities' => 'Opportunità totali:',
        'totalPipeline' => 'Pipeline totale:',
        'won' => 'Vinte:',
        'wonValue' => 'Valore vinto:',
    ],
];
