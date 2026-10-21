<?php

declare(strict_types=1);

it('keeps the company concept out of the crm migrations', function (): void {
    $offenders = [];

    foreach (glob(module_path('CRM', 'database/migrations/*.php')) ?: [] as $migration) {
        if (str_contains((string) file_get_contents($migration), 'company')) {
            $offenders[] = basename($migration);
        }
    }

    expect($offenders)->toBe([]);
});
