<?php

declare(strict_types=1);

namespace Modules\CRM\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Support\ModuleColor;

final class CRMPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'CRM';
    }

    public function getId(): string
    {
        return 'crm';
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Own the module's presence in the panel: the navigation groups, their icons and the module colour
     * (registered under the module id, so widgets can paint with it) are declared here.
     */
    public function afterRegister(Panel $panel): void
    {
        $color = ModuleColor::filament($this->getModuleName());

        if ($color !== null) {
            $panel->colors([$this->getId() => $color]);
        }

        $panel->navigationGroups([
            'crm.navigation.master_data' => NavigationGroup::make()->label(static fn (): string => (string) __('crm.navigation.master_data'))->icon(Heroicon::OutlinedUsers),
            'crm.navigation.pipeline' => NavigationGroup::make()->label(static fn (): string => (string) __('crm.navigation.pipeline'))->icon(Heroicon::OutlinedFunnel),
        ]);
    }
}
