<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Helpers\MigrateUtils;
use Modules\CRM\Enums\CRMTables;

return new class extends Migration
{
    public function up(): void
    {
        $bank_accounts_table = CRMTables::CounterpartyBankAccounts->value;
        Schema::create($bank_accounts_table, function (Blueprint $table) use ($bank_accounts_table): void {
            $table->id();
            $table->foreignId('counterparty_id')
                ->constrained(CRMTables::Counterparties->value, 'id', "{$bank_accounts_table}_counterparty_id_FK")
                ->cascadeOnDelete();
            MigrateUtils::prefixIndex($table, 'counterparty_id');
            $table->string('beneficiary_name');
            $table->string('iban', 34);
            $table->string('bic', 11)->nullable();
            $table->char('currency', 3)->default('EUR');
            $table->boolean('is_active')->default(true);
            $table->unique(['counterparty_id', 'iban'], "{$bank_accounts_table}_counterparty_iban_UN");
            MigrateUtils::timestamps($table, hasCreateUpdate: true, hasSoftDelete: true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(CRMTables::CounterpartyBankAccounts->value);
    }
};
