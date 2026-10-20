<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Helpers\MigrateUtils;
use Modules\CRM\Enums\CRMTables;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $contactables_table = CRMTables::Contactables->value;
        Schema::create($contactables_table, function (Blueprint $table) use ($contactables_table): void {
            $table->foreignId('counterparty_id')
                ->constrained(CRMTables::Counterparties->value, 'id', "{$contactables_table}_counterparty_id_FK")
                ->nullable(false)
                ->cascadeOnDelete()
                ->comment('The counterparty that the contact works for');
            $table->foreignId('contact_id')
                ->constrained(CRMTables::Contacts->value, 'id', "{$contactables_table}_contact_id_FK")
                ->nullable(false)
                ->cascadeOnDelete()
                ->comment('The contact that the contactable belongs to');
            MigrateUtils::prefixIndex($table, 'contact_id');
            $table->primary(['counterparty_id', 'contact_id'], "{$contactables_table}_primary_idx")->comment('The primary key of the contactable relationship');
            MigrateUtils::timestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(CRMTables::Contactables->value);
    }
};
