<?php

declare(strict_types=1);

use Modules\CRM\Casts\OpportunityStatus;
use Modules\CRM\Filament\Pages\SalesPipelinePage;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Opportunity;
use Modules\CRM\Services\SalesPipelineCsvExporter;
use Modules\CRM\Services\SalesPipelineService;
use Modules\CRM\Tests\Support\OpportunityStageTaxonomy;

it('summarizes sales pipeline values by opportunity status', function (): void {
    $stage_id = OpportunityStageTaxonomy::insertMinimalId('pipeline-summary');
    $counterparty = Counterparty::factory()->create();

    Opportunity::query()->create([
        'counterparty_id' => $counterparty->getKey(),
        'stage_taxonomy_id' => $stage_id,
        'name' => 'Open deal',
        'status' => OpportunityStatus::Open->value,
        'expected_value_doc' => '1000.5000',
        'expected_currency_doc' => 'EUR',
        'expected_value_local' => '1000.5000',
        'expected_currency_local' => 'EUR',
        'expected_fx_rate' => '1.00000000',
        'probability' => 50,
    ]);
    Opportunity::query()->create([
        'counterparty_id' => $counterparty->getKey(),
        'stage_taxonomy_id' => $stage_id,
        'name' => 'Won deal',
        'status' => OpportunityStatus::Won->value,
        'expected_value_doc' => '250.2500',
        'expected_currency_doc' => 'EUR',
        'expected_value_local' => '250.2500',
        'expected_currency_local' => 'EUR',
        'expected_fx_rate' => '1.00000000',
        'probability' => 100,
        'won_at' => now(),
    ]);
    Opportunity::query()->create([
        'counterparty_id' => $counterparty->getKey(),
        'stage_taxonomy_id' => $stage_id,
        'name' => 'Lost deal',
        'status' => OpportunityStatus::Lost->value,
        'expected_value_doc' => null,
        'expected_currency_doc' => 'EUR',
        'expected_value_local' => null,
        'expected_currency_local' => 'EUR',
        'expected_fx_rate' => '1.00000000',
        'probability' => 0,
        'lost_at' => now(),
    ]);

    $result = app(SalesPipelineService::class)->generate();

    expect($result['total_count'])->toBe(3)
        ->and($result['won_count'])->toBe(1)
        ->and($result['lost_count'])->toBe(1)
        ->and($result['total_expected_value_doc'])->toBe('1250.7500')
        ->and($result['total_expected_value_local'])->toBe('1250.7500')
        ->and($result['by_status'][OpportunityStatus::Open->value]['count'])->toBe(1)
        ->and($result['by_status'][OpportunityStatus::Open->value]['expected_value_local'])->toBe('1000.5000')
        ->and($result['by_status'][OpportunityStatus::Won->value]['count'])->toBe(1)
        ->and($result['by_status'][OpportunityStatus::Lost->value]['expected_value_local'])->toBe('0.0000')
        ->and($result['by_status'][OpportunityStatus::Cancelled->value]['count'])->toBe(0);
});

it('filters won pipeline metrics by won date range without dropping open pipeline', function (): void {
    $stage_id = OpportunityStageTaxonomy::insertMinimalId('pipeline-won-range');
    $counterparty = Counterparty::factory()->create();

    Opportunity::query()->create([
        'counterparty_id' => $counterparty->getKey(),
        'stage_taxonomy_id' => $stage_id,
        'name' => 'Open current pipeline',
        'status' => OpportunityStatus::Open->value,
        'expected_value_doc' => '1000.0000',
        'expected_currency_doc' => 'EUR',
        'expected_value_local' => '1000.0000',
        'expected_currency_local' => 'EUR',
        'expected_fx_rate' => '1.00000000',
        'probability' => 50,
    ]);
    Opportunity::query()->create([
        'counterparty_id' => $counterparty->getKey(),
        'stage_taxonomy_id' => $stage_id,
        'name' => 'Won inside range',
        'status' => OpportunityStatus::Won->value,
        'expected_value_doc' => '250.0000',
        'expected_currency_doc' => 'EUR',
        'expected_value_local' => '250.0000',
        'expected_currency_local' => 'EUR',
        'expected_fx_rate' => '1.00000000',
        'probability' => 100,
        'won_at' => '2026-02-15 12:00:00',
    ]);
    Opportunity::query()->create([
        'counterparty_id' => $counterparty->getKey(),
        'stage_taxonomy_id' => $stage_id,
        'name' => 'Won outside range',
        'status' => OpportunityStatus::Won->value,
        'expected_value_doc' => '900.0000',
        'expected_currency_doc' => 'EUR',
        'expected_value_local' => '900.0000',
        'expected_currency_local' => 'EUR',
        'expected_fx_rate' => '1.00000000',
        'probability' => 100,
        'won_at' => '2025-12-31 12:00:00',
    ]);

    $result = app(SalesPipelineService::class)->generate([
        'won_from' => '2026-02-01',
        'won_to' => '2026-02-28',
    ]);

    expect($result['total_count'])->toBe(3)
        ->and($result['by_status'][OpportunityStatus::Open->value]['count'])->toBe(1)
        ->and($result['won_count'])->toBe(1)
        ->and($result['won_value_doc'])->toBe('250.0000')
        ->and($result['won_value_local'])->toBe('250.0000');
});

it('exports the sales pipeline as csv', function (): void {
    $csv = resolve(SalesPipelineCsvExporter::class)->export([
        'by_status' => [
            OpportunityStatus::Open->value => [
                'status' => OpportunityStatus::Open->value,
                'count' => 1,
                'expected_value_doc' => '1000.0000',
                'expected_value_local' => '1000.0000',
            ],
        ],
        'total_count' => 1,
        'won_count' => 0,
        'lost_count' => 0,
        'won_value_doc' => '0.0000',
        'won_value_local' => '0.0000',
        'total_expected_value_doc' => '1000.0000',
        'total_expected_value_local' => '1000.0000',
    ]);

    expect($csv)->toContain('Status,Count,"Expected doc","Expected local"')
        ->and($csv)->toContain('open,1,1000.0000,1000.0000');
});

it('exposes the sales pipeline page with a csv export action', function (): void {
    expect(method_exists(SalesPipelinePage::class, 'exportCsv'))->toBeTrue()
        ->and(SalesPipelinePage::getUrl())->toEndWith('/admin/sales-pipeline');
});

it('renders the sales pipeline page for an authenticated admin', function (): void {
    $admin = App\Models\User::factory()->create([
        'email' => 'crm-pipeline@example.com',
        'email_verified_at' => now(),
        'password' => 'Aa1!FilamentAdminPass',
    ]);
    $admin->roles()->attach(Modules\Core\Models\Role::factory()->create(['name' => 'superadmin', 'guard_name' => 'web']));
    $this->actingAs($admin, 'admin');

    $this->get(SalesPipelinePage::getUrl())->assertOk();
});
