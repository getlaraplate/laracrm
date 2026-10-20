<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Enums\CoreTables;
use Modules\Core\Helpers\MigrateUtils;
use Modules\CRM\Enums\CRMTables;
use Modules\CRM\Casts\LeadStatus;

return new class extends Migration
{
    public function up(): void
    {
        $leads_table = CRMTables::Leads->value;
        Schema::create($leads_table, function (Blueprint $table) use ($leads_table): void {
            $table->id();
            $table->foreignId('counterparty_id')
                ->nullable()
                ->constrained(CRMTables::Counterparties->value, 'id', "{$leads_table}_counterparty_id_FK")
                ->nullOnDelete()
                ->comment('Linked counterparty once known (optional on cold leads)');
            MigrateUtils::prefixIndex($table, 'counterparty_id');
            $table->foreignId('contact_id')
                ->nullable()
                ->constrained(CRMTables::Contacts->value, 'id', "{$leads_table}_contact_id_FK")
                ->nullOnDelete()
                ->comment('Primary contact person when known');
            MigrateUtils::prefixIndex($table, 'contact_id');
            $table->string('title')->comment('Short label for the lead');
            $table->string('source', 128)->nullable()->comment('e.g. web, referral, partner');
            $table->enum('status', array_map(
                static fn (LeadStatus $s): string => $s->value,
                LeadStatus::cases(),
            ))->default(LeadStatus::New->value)->index("{$leads_table}_status_IDX");
            $table->foreignId('owner_user_id')
                ->nullable()
                ->constrained(CoreTables::Users->value, 'id', "{$leads_table}_owner_user_id_FK")
                ->nullOnDelete();
            MigrateUtils::prefixIndex($table, 'owner_user_id');
            $table->text('notes')->nullable();
            $table->timestamp('converted_at')->nullable()->comment('When the lead moved to an opportunity or party flow');

            MigrateUtils::timestamps(
                $table,
                hasCreateUpdate: true,
                hasSoftDelete: true,
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(CRMTables::Leads->value);
    }
};
