# CRM Module: Developer Reference

Commercial relationship module of Laraplate: counterparties, contacts, leads and opportunities.

## Boundaries

- **Depends on Core only.** The ERP depends on the CRM (`ERP/module.json` requires `CRM`); the CRM imports no ERP class and reads no ERP table.
- Native module: same schema and connection as the other native modules; cross-module foreign keys are allowed (`erp_parties.counterparty_id` points to `crm_counterparties`). It does not use or extend `ConnectionScoped*`.
- No `Company` and no tenant in the CRM. Multi-tenant SaaS is one database per tenant; companies of a group share counterparties, contacts, leads and opportunities.
- Tables are prefixed `crm_` and listed in `Modules\CRM\Enums\CRMTables`; migrations are stamped `2026_04_08_09xxxx`, before the ERP ones.

## Models

| Model | Table | Notes |
|-------|-------|-------|
| `Counterparty` | `crm_counterparties` | Legal identity, `kind`, optional `first_name` / `last_name` for persons (`name` is derived as "last first"; refused on a company), optional parent, aggregate flags. `Counterparty::findByFiscalIdentity($country, $vat, $taxId)` matches country plus VAT number, then tax id; blank identifiers never match. A counterparty cannot be its own ancestor. |
| `CounterpartyBankAccount` | `crm_counterparty_bank_accounts` | Unique per (`counterparty_id`, `iban`); IBAN, BIC and currency normalised on save. |
| `Contact`, `Pivot\Contactable` | `crm_contacts`, `crm_contactables` | Many-to-many with counterparties. The link has `role_taxonomy_id` (a `ContactRole` node) and `is_primary`; the pivot model keeps one primary per counterparty by demoting the previous one. The counterparty edit page lists its contacts with role and primary flag. |
| `Lead` | `crm_leads` | `counterparty_id` nullable. |
| `Opportunity` | `crm_opportunities` | `counterparty_id` required, `restrictOnDelete`; no company. |
| `OpportunityStage`, `Entity`, `Preset`, `Pivot\Presettable`, `Casts\EntityType` | Core `taxonomies`, `core_entities`, `core_presets`, `core_presettables` | The per-module taxonomy pattern also used by Shop. |

## Integration with the ERP

- `Party` uses `ProxiesCounterparty`: `$party->name`, `$party->vat_number` and the other identity attributes read and write the counterparty; saving without a `counterparty_id` attaches by fiscal identity or creates one, and never overwrites the stored attributes of an existing counterparty.
- `LeadConversionService::convert()`: lead to open opportunity in one transaction (decision D30); the leads list exposes it as the "Convert" action.
- `PartyObserver` recomputes the counterparty aggregate flags when a party is created, deleted, restored, or when its flags or its counterparty change (the counterparty it leaves is refreshed too). `GuardCounterpartyDeletion` refuses to delete a counterparty with a live party; `GuardOrganisationalCounterparty` refuses a group parent without VAT number and tax id that already has a party (the party side refuses to attach to such a counterparty when it has children).
- `QuotationObserver`: an accepted quotation sets `is_customer` on its party and calls `OpportunityLifecycleService::markWon()`. A quotation no longer requires a customer party, and its opportunity must belong to the same counterparty as the quotation party.
- `PartyBankAccount` (ERP) points to a `CounterpartyBankAccount` and proxies its data through `ProxiesCounterpartyBankAccount`.
- `Party` implements Core `IMountsRelatedAttributes`: the CRUD API filters, sorts and selects the identity attributes by their plain name (`name`, `vat_number`); Core turns them into the `counterparty` relation column.
- The sales pipeline report (`SalesPipelineService`, `SalesPipelinePage` at `/admin/sales-pipeline`, `SalesPipelineCsvExporter` over Core `TabularCsvExporter`) lives in the CRM, in the pipeline navigation group, with won-date filters and a CSV export.

## Filament

`CRMPlugin` (id `crm`) registers the groups `crm.navigation.master_data` (counterparties, contacts) and `crm.navigation.pipeline` (leads, opportunities). The counterparty list shows the customer and supplier flags as icons and hides suppliers that are not customers behind a default filter the user can clear. Labels live in `lang/{de,en,es,it,sl}/crm.php`.

## Seeding and permissions

`CRMDatabaseSeeder` creates the `opportunity_stage` entity and its preset once. `DevCRMDatabaseSeeder` seeds the five stages and a demo prospect (a counterparty without a party), lead and opportunity. CRUD permissions of the models come from `permission:refresh`; the bank accounts have their own `CounterpartyBankAccount` abilities. Row-level access and per-user allowed companies are not built yet.

## Not in this module yet

Quote and product catalogue for a CRM used without the ERP (the `Quotation` stays in the ERP, decision D14), group totals over the hierarchy, automatic twin documents and consolidation eliminations for intercompany sales (a party of another company of the group is recognised, see decision D29), printing through Core templates.
