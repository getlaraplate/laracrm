<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Enums\CoreTables;
use Modules\Core\Helpers\MigrateUtils;
use Modules\CRM\Casts\OpportunityStatus;
use Modules\CRM\Enums\CRMTables;

return new class extends Migration
{
    public function up(): void
    {
        $opportunities_table = CRMTables::Opportunities->value;
        Schema::create($opportunities_table, function (Blueprint $table) use ($opportunities_table): void {
            $table->id();
            $table->foreignId('lead_id')
                ->nullable()
                ->constrained(CRMTables::Leads->value, 'id', "{$opportunities_table}_lead_id_FK")
                ->nullOnDelete();
            MigrateUtils::prefixIndex($table, 'lead_id');
            $table->foreignId('counterparty_id')
                ->nullable(false)
                ->constrained(CRMTables::Counterparties->value, 'id', "{$opportunities_table}_counterparty_id_FK")
                ->restrictOnDelete();
            MigrateUtils::prefixIndex($table, 'counterparty_id');
            $table->foreignId('stage_taxonomy_id')
                ->nullable(false)
                ->constrained(CoreTables::Taxonomies->value, 'id', "{$opportunities_table}_stage_taxonomy_id_FK")
                ->restrictOnDelete()
                ->comment('Pipeline stage; use EntityType::OpportunityStages tree');
            MigrateUtils::prefixIndex($table, 'stage_taxonomy_id');
            $table->string('name')->comment('Opportunity title');
            $table->enum('status', array_map(
                static fn (OpportunityStatus $s): string => $s->value,
                OpportunityStatus::cases(),
            ))->default(OpportunityStatus::Open->value)->index("{$opportunities_table}_status_IDX");
            $table->date('expected_close_date')->nullable();
            $table->decimal('expected_value_doc', 15, 4)->nullable()->comment('Weighted expected value in document currency');
            $table->char('expected_currency_doc', 3)->default('EUR');
            $table->decimal('expected_value_local', 15, 4)->nullable()->comment('Expected value in the local (functional) currency');
            $table->char('expected_currency_local', 3)->default('EUR');
            $table->decimal('expected_fx_rate', 18, 8)->default(1);
            $table->unsignedTinyInteger('probability')->nullable()->comment('Win probability 0–100');
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->text('lost_reason')->nullable();

            MigrateUtils::timestamps(
                $table,
                hasCreateUpdate: true,
                hasSoftDelete: true,
            );

            $table->index(['company_id', 'counterparty_id'], "{$opportunities_table}_company_counterparty_idx");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(CRMTables::Opportunities->value);
    }
};
