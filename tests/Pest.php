<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CRM\Tests\TestCase;

pest()->extend(TestCase::class)
    ->in(__DIR__ . '/Unit', __DIR__ . '/Feature');

pest()->use(RefreshDatabase::class)
    ->in(__DIR__ . '/Feature');
