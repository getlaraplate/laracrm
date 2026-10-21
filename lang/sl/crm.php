<?php

declare(strict_types=1);

return [
    'actions' => [
        'convert' => 'Pretvori v priložnost',
    ],
    'entities' => [
        'contacts' => [
            'plural' => 'Kontakti',
            'singular' => 'Kontakt',
        ],
        'counterparties' => [
            'plural' => 'Nasprotne stranke',
            'singular' => 'Nasprotna stranka',
        ],
        'leads' => [
            'plural' => 'Potencialne stranke',
            'singular' => 'Potencialna stranka',
        ],
        'opportunities' => [
            'plural' => 'Priložnosti',
            'singular' => 'Priložnost',
        ],
    ],
    'fields' => [
        'address_line' => 'Naslov',
        'city' => 'Mesto',
        'counterparty_id' => 'Nasprotna stranka',
        'counterparty_ids' => 'Nasprotne stranke',
        'country' => 'Država',
        'einvoice_pec_email' => 'E-pošta PEC',
        'einvoice_recipient_code' => 'Koda prejemnika',
        'first_name' => 'Osebno ime',
        'fiscal_country' => 'Davčna država',
        'is_customer' => 'Stranka',
        'is_primary' => 'Glavni kontakt',
        'is_supplier' => 'Dobavitelj',
        'kind' => 'Vrsta',
        'last_name' => 'Priimek',
        'name' => 'Ime',
        'new_counterparty_fiscal_country' => 'Država novega podjetja',
        'new_counterparty_name' => 'Ime novega podjetja',
        'new_counterparty_tax_id' => 'Davčna številka novega podjetja',
        'new_counterparty_vat_number' => 'DDV številka novega podjetja',
        'notes' => 'Opombe',
        'owner_user_id' => 'Odgovorna oseba',
        'parent_counterparty_id' => 'Nadrejena nasprotna stranka',
        'postal_code' => 'Poštna številka',
        'province' => 'Pokrajina',
        'role_taxonomy_id' => 'Vloga',
        'stage' => 'Faza',
        'stage_taxonomy_id' => 'Faza',
        'status' => 'Stanje',
        'tax_id' => 'Davčna številka',
        'vat_number' => 'ID za DDV',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Skrij dobavitelje, ki niso stranke',
    ],
    'help' => [
        'personNameDerived' => 'Izpeljano kot "priimek ime", ko je izpolnjeno eno od polj.',
        'stageTaxonomiesRequired' => 'Faze prodajnega lijaka zahtevajo taksonomije: zaženite CRMDatabaseSeeder in nato DevCRMDatabaseSeeder.',
    ],
    'messages' => [
        'leadConverted' => 'Potencialna stranka je pretvorjena v priložnost.',
    ],
    'reports' => [
        'count' => 'Število',
        'expectedDoc' => 'Pričakovano (dokument)',
        'expectedLocal' => 'Pričakovano (domača valuta)',
        'lost' => 'Izgubljene:',
        'lostCount' => 'Število izgubljenih:',
        'noPipelineData' => 'Za izbrane filtre ni podatkov o lijaku.',
        'pipelineSummary' => 'Povzetek lijaka',
        'status' => 'Stanje',
        'total' => 'Skupaj',
        'totalOpportunities' => 'Skupaj priložnosti:',
        'totalPipeline' => 'Skupaj lijak:',
        'won' => 'Pridobljene:',
        'wonValue' => 'Pridobljena vrednost:',
    ],
    'pages' => [
        'salesPipeline' => 'Prodajni lijak',
    ],
    'navigation' => [
        'master_data' => 'CRM - Matični podatki',
        'pipeline' => 'CRM - Prodajni lijak',
    ],
];
