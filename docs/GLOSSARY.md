# CRM Glossary

## Subjects

| Term | Meaning |
|------|---------|
| **Counterparty** | A company or a natural person (`kind`: `company` or `person`), shared by every company of the installation. Table `crm_counterparties`. Holds name, VAT number, tax id, fiscal country, address, e-invoice routing code and PEC, and an optional `parent_counterparty_id` for groups. Soft deleted; it cannot be deleted while a live ERP `Party` points to it. |
| **Aggregate flags** | `is_customer` and `is_supplier` on a counterparty mean "at least one of its ERP parties has the flag". The ERP writes them; they are read-only in the CRM. |
| **Party (ERP)** | The relationship between a counterparty and one company. Created by the ERP; it reads and writes the legal identity through the counterparty and attaches to an existing counterparty with the same VAT number (or tax id) instead of creating a second one. |
| **Organisational counterparty** | A counterparty with children and neither a VAT number nor a tax id: it groups legal entities and cannot have a party. |
| **CounterpartyBankAccount** | A bank account of a counterparty (`beneficiary_name`, `iban`, `bic`, `currency`, `is_active`), stored once. Table `crm_counterparty_bank_accounts`. The ERP `PartyBankAccount` points to it and keeps the per-company default flag and direct debit mandate. |
| **Contact** | A person at one or more counterparties through `crm_contactables`; may be linked to a Laraplate user. |

## Pipeline

| Term | Meaning |
|------|---------|
| **Lead** | An early-stage approach to a counterparty (optional on a cold lead) with `LeadStatus` (new, contacted, qualified, disqualified, converted). |
| **Opportunity** | A qualified deal with a counterparty (required), a pipeline stage, `OpportunityStatus` (open, won, lost, cancelled) and expected values in document and local currency. It has no company: one opportunity can produce quotations for different companies. |
| **OpportunityStage** | A pipeline stage; a Core `Taxonomy` of the `opportunity_stages` entity type, seeded by `CRMDatabaseSeeder` (entity and preset) and `DevCRMDatabaseSeeder` (the stage tree). |
| **OpportunityLifecycleService** | `markWon($opportunity_id)` sets the status to won once and keeps the first win date; an unknown id is ignored. |

## Module

| Term | Meaning |
|------|---------|
| **CRMTables** | Enum of the module tables (`crm_` prefix); the case name is the plural of the model class name. |
| **CRMPlugin** | Filament plugin with the navigation groups `crm.navigation.master_data` and `crm.navigation.pipeline`. |
