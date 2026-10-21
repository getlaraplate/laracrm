<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Helpers\MigrateUtils;
use Modules\CRM\Casts\CounterpartyKind;
use Modules\CRM\Enums\CRMTables;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $counterparties_table = CRMTables::Counterparties->value;
        Schema::create($counterparties_table, function (Blueprint $table) use ($counterparties_table): void {
            $table->id();
            $table->string('name')->comment('Display name of the counterparty; for a person with name parts, derived as "last first"');
            $table->string('first_name', 128)->nullable()->comment('Given name, natural persons only');
            $table->string('last_name', 128)->nullable()->comment('Family name, natural persons only');
            $table->enum('kind', CounterpartyKind::values())->default(CounterpartyKind::Company->value)->comment('Company or natural person');
            $table->string('tax_id', 32)->nullable()->index("{$counterparties_table}_tax_id_idx");
            $table->string('vat_number', 32)->nullable()->index("{$counterparties_table}_vat_number_idx");
            $table->string('fiscal_country', 2)->nullable();
            $table->string('address_line')->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->string('city', 128)->nullable();
            $table->string('province', 8)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('einvoice_recipient_code', 7)->nullable()->index("{$counterparties_table}_sdi_code_idx");
            $table->string('einvoice_pec_email')->nullable();
            $table->boolean('is_customer')->default(false)->comment('Aggregate written by the ERP: at least one party is a customer');
            $table->boolean('is_supplier')->default(false)->comment('Aggregate written by the ERP: at least one party is a supplier');
            $table->foreignId('parent_counterparty_id')
                ->nullable()
                ->constrained($counterparties_table, 'id', "{$counterparties_table}_parent_counterparty_id_FK")
                ->nullOnDelete()
                ->comment('Optional group hierarchy');
            MigrateUtils::prefixIndex($table, 'parent_counterparty_id');

            MigrateUtils::timestamps(
                $table,
                hasCreateUpdate: true,
                hasSoftDelete: true,
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(CRMTables::Counterparties->value);
    }
};
