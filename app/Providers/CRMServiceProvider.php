<?php

declare(strict_types=1);

namespace Modules\CRM\Providers;

use Modules\Core\Overrides\ModuleServiceProvider;
use Override;

final class CRMServiceProvider extends ModuleServiceProvider
{
    #[Override]
    protected string $name = 'CRM';

    #[Override]
    protected string $nameLower = 'crm';

    /**
     * Provider classes to register.
     *
     * @var array<int, class-string>
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
