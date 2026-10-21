<?php

declare(strict_types=1);

return [
    'entities' => [
        'counterparties' => [
            'plural' => 'Contrapartes',
            'singular' => 'Contraparte',
        ],
        'contacts' => [
            'plural' => 'Contactos',
            'singular' => 'Contacto',
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
        'new_counterparty_name' => 'Nombre de la nueva empresa',
        'new_counterparty_fiscal_country' => 'País de la nueva empresa',
        'new_counterparty_vat_number' => 'NIF/IVA de la nueva empresa',
        'new_counterparty_tax_id' => 'Identificación fiscal de la nueva empresa',
        'notes' => 'Notas',
        'owner_user_id' => 'Responsable',
        'stage' => 'Etapa',
        'stage_taxonomy_id' => 'Etapa',
        'is_customer' => 'Cliente',
        'is_supplier' => 'Proveedor',
        'vat_number' => 'NIF-IVA',
        'tax_id' => 'Código fiscal',
        'fiscal_country' => 'País fiscal',
        'address_line' => 'Dirección',
        'postal_code' => 'Código postal',
        'city' => 'Ciudad',
        'province' => 'Provincia',
        'country' => 'País',
        'einvoice_recipient_code' => 'Código de destinatario',
        'einvoice_pec_email' => 'Correo PEC',
        'name' => 'Nombre',
        'status' => 'Estado',
        'kind' => 'Tipo',
        'parent_counterparty_id' => 'Contraparte matriz',
        'counterparty_id' => 'Contraparte',
        'counterparty_ids' => 'Contrapartes',
    ],
    'filters' => [
        'hidePureSuppliers' => 'Ocultar proveedores que no son clientes',
    ],
    'navigation' => [
        'master_data' => 'CRM - Datos maestros',
        'pipeline' => 'CRM - Embudo de ventas',
    ],
    'help' => [
        'stageTaxonomiesRequired' => 'Las fases del embudo requieren taxonomías: ejecuta CRMDatabaseSeeder y luego DevCRMDatabaseSeeder.',
    ],
];
