<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Modules\Core\Models\Permission;
use Modules\Core\Support\PermissionName;
use Modules\CRM\Casts\EntityType;
use Modules\CRM\Database\Seeders\CRMDatabaseSeeder;
use Modules\CRM\Database\Seeders\DevCRMDatabaseSeeder;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Entity;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Opportunity;
use Modules\CRM\Models\OpportunityStage;
use Modules\CRM\Models\Preset;

it('seeds the opportunity stage entity and preset once', function (): void {
    $this->seed(CRMDatabaseSeeder::class);
    $this->seed(CRMDatabaseSeeder::class);

    $entities = Entity::query()->withoutGlobalScopes()->where('type', EntityType::OpportunityStages->value)->get();

    expect($entities)->toHaveCount(1)
        ->and($entities->first()->name)->toBe('opportunity_stage')
        ->and(Preset::query()->withoutGlobalScopes()->where('entity_id', $entities->first()->getKey())->count())->toBe(1);
});

it('seeds crud permissions for the crm entities', function (): void {
    Artisan::call('permission:refresh');

    $names = Permission::query()->pluck('name')->all();

    foreach ([Counterparty::class, Contact::class, Lead::class, Opportunity::class] as $model) {
        expect($names)->toContain(PermissionName::forClass($model, 'select'));
    }
});

it('seeds the demo pipeline once: stages, a prospect without a party, a lead and an opportunity', function (): void {
    $this->seed(CRMDatabaseSeeder::class);
    $this->seed(DevCRMDatabaseSeeder::class);
    $this->seed(DevCRMDatabaseSeeder::class);

    expect(OpportunityStage::query()->count())->toBe(5)
        ->and(Counterparty::query()->where('tax_id', '55566677788')->count())->toBe(1)
        ->and(Lead::query()->count())->toBe(1)
        ->and(Opportunity::query()->count())->toBe(1)
        ->and(Opportunity::query()->firstOrFail()->counterparty->name)->toBe('Prospect Demo S.r.l.');
});
