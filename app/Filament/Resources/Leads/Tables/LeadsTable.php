<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Leads\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Modules\Core\Filament\Utils\HasTable;

final class LeadsTable
{
    use HasTable;

    public static function configure(Table $table): Table
    {
        return self::configureTable(
            table: $table,
            columns: static function (Collection $default_columns): void {
                $default_columns->unshift(...[
                    TextColumn::make('title')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('status')
                        ->badge()
                        ->sortable(),
                    TextColumn::make('counterparty.name')
                        ->toggleable(),
                    TextColumn::make('source')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]);
            },
        );
    }
}
