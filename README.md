<p>&nbsp;</p>
<p align="center">
	<a href="https://github.com/getlaraplate" target="_blank">
		<img src="https://raw.githubusercontent.com/getlaraplate/brand/main/logos/blueprint/crm.svg" width="160" alt="LaraCRM" />
    </a>
</p>
<p>&nbsp;</p>

# Laraplate CRM Module

> ⚠️ **Caution**: This package is a **work in progress**. **Don't use this in production or use at your own risk**—no guarantees are provided... or better yet, collaborate with me to create the definitive Laravel boilerplate; that's the right place to introduce your ideas. Let me know your ideas...

## Description

The CRM module owns the commercial relationship with a counterparty: who it is, who works there, and the sales pipeline. It is upstream of the ERP module: the ERP depends on the CRM, never the reverse, and the CRM imports no ERP class and reads no ERP table.

- **Counterparty**: a company or a natural person, shared by every company of the installation. Holds the legal identity (name, VAT number, tax id, address, e-invoice routing), an optional parent for groups, the bank accounts, and the aggregate customer/supplier flags written by the ERP.
- **Contact**: a person at one or more counterparties, optionally a Laraplate user.
- **Lead** and **Opportunity**: the pipeline, with `OpportunityStage` as a Core taxonomy.
- **OpportunityLifecycleService**: closes an opportunity as won; the ERP calls it when a quotation is accepted.

The ERP `Party` is the relationship between a counterparty and one company of the group; it reads and writes its legal identity through the counterparty.

## Installation

Install **Core** first, then this module, then the ERP. Enable it with `php artisan module:enable CRM`; migrations run before the ERP ones (their file names are stamped earlier), and `php artisan permission:refresh` creates the CRUD permissions of its models.

## Current Status

Parity with the former ERP M3.1 CRM, plus counterparties and counterparty bank accounts. Quotations, a product catalogue, campaigns, interaction history and ticketing are not built yet; see `docs/superpowers/specs/2026-10-15-crm-module-design.md` in the `laraplate` repository.

## License

GNU AGPL v3.
