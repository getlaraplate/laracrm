<?php

declare(strict_types=1);

return [
    'entities' => [
        'contacts' => [
            'plural' => 'Contactos',
            'singular' => 'Contacto',
        ],
        'counterparties' => [
            'plural' => 'Contrapartes',
            'singular' => 'Contraparte',
        ],
        'leads' => [
            'plural' => 'Leads',
            'singular' => 'Lead',
        ],
        'opportunities' => [
            'plural' => 'Oportunidades',
            'singular' => 'Oportunidad',
        ],
    ],
    'fields' => [
        'address_line' => 'Dirección',
        'city' => 'Ciudad',
        'counterparty_id' => 'Contraparte',
        'counterparty_ids' => 'Contrapartes',
        'country' => 'País',
        'einvoice_pec_email' => 'Correo PEC',
        'einvoice_recipient_code' => 'Código de destinatario',
        'fiscal_country' => 'País fiscal',
        'is_customer' => 'Cliente',
        'is_supplier' => 'Proveedor',
        'kind' => 'Tipo',
        'name' => 'Nombre',
        'new_counterparty_fiscal_country' => 'País de la nueva empresa',
        'new_counterparty_name' => 'Nombre de la nueva empresa',
        'new_counterparty_tax_id' => 'Identificación fiscal de la nueva empresa',
        'new_counterparty_vat_number' => 'NIF/IVA de la nueva empresa',
        'notes' => 'Notas',
        'owner_user_id' => 'Responsable',
        'parent_counterparty_id' => 'Contraparte matriz',
        'postal_code' => 'Código postal',
        'province' => 'Provincia',
        'stage' => 'Etapa',
        'stage_taxonomy_id' => 'Etapa',
        'status' => 'Estado',
        'tax_id' => 'Código fiscal',
        'vat_number' => 'NIF-IVA',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Ocultar proveedores que no son clientes',
    ],
    'help' => [
        'stageTaxonomiesRequired' => 'Las fases del embudo requieren taxonomías: ejecuta CRMDatabaseSeeder y luego DevCRMDatabaseSeeder.',
    ],
    'navigation' => [
        'master_data' => 'CRM - Datos maestros',
        'pipeline' => 'CRM - Embudo de ventas',
    ],
];
