<?php

declare(strict_types=1);

return [
    'entities' => [
        'counterparties' => [
            'plural' => 'Nasprotne stranke',
            'singular' => 'Nasprotna stranka',
        ],
        'contacts' => [
            'plural' => 'Kontakti',
            'singular' => 'Kontakt',
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
        'new_counterparty_name' => 'Ime novega podjetja',
        'new_counterparty_fiscal_country' => 'Država novega podjetja',
        'new_counterparty_vat_number' => 'DDV številka novega podjetja',
        'new_counterparty_tax_id' => 'Davčna številka novega podjetja',
        'notes' => 'Opombe',
        'owner_user_id' => 'Odgovorna oseba',
        'stage' => 'Faza',
        'stage_taxonomy_id' => 'Faza',
        'is_customer' => 'Stranka',
        'is_supplier' => 'Dobavitelj',
        'vat_number' => 'ID za DDV',
        'tax_id' => 'Davčna številka',
        'fiscal_country' => 'Davčna država',
        'address_line' => 'Naslov',
        'postal_code' => 'Poštna številka',
        'city' => 'Mesto',
        'province' => 'Pokrajina',
        'country' => 'Država',
        'einvoice_recipient_code' => 'Koda prejemnika',
        'einvoice_pec_email' => 'E-pošta PEC',
        'name' => 'Ime',
        'status' => 'Stanje',
        'kind' => 'Vrsta',
        'parent_counterparty_id' => 'Nadrejena nasprotna stranka',
        'counterparty_id' => 'Nasprotna stranka',
        'counterparty_ids' => 'Nasprotne stranke',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Skrij dobavitelje, ki niso stranke',
    ],
    'navigation' => [
        'master_data' => 'CRM - Matični podatki',
        'pipeline' => 'CRM - Prodajni lijak',
    ],
    'help' => [
        'stageTaxonomiesRequired' => 'Faze prodajnega lijaka zahtevajo taksonomije: zaženite CRMDatabaseSeeder in nato DevCRMDatabaseSeeder.',
    ],
];
