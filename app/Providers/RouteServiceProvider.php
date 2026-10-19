<?php

declare(strict_types=1);

namespace Modules\CRM\Providers;

use Modules\Core\Overrides\RouteServiceProvider as ServiceProvider;
use Override;

final class RouteServiceProvider extends ServiceProvider
{
    #[Override]
    protected string $name = 'CRM';
}
