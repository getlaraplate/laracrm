<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Counterparties\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Core\Filament\Utils\HasTable;

final class CounterpartiesTable
{
    use HasTable;

    public static function configure(Table $table): Table
    {
        return self::configureTable(
            table: $table,
            columns: static function (Collection $default_columns): void {
                $default_columns->unshift(...[
                    TextColumn::make('name')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('kind')
                        ->badge()
                        ->toggleable(),
                    TextColumn::make('vat_number')
                        ->searchable()
                        ->toggleable(),
                    TextColumn::make('city')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    IconColumn::make('is_customer')
                        ->boolean(),
                    IconColumn::make('is_supplier')
                        ->boolean(),
                ]);
            },
            filters: static function (Collection $default_filters): void {
                $default_filters->put('hide_pure_suppliers', TernaryFilter::make('hide_pure_suppliers')
                    ->label((string) __('crm.filters.hidePureSuppliers'))
                    ->default(true)
                    ->queries(
                        true: static fn (Builder $query): Builder => $query->where(
                            static fn (Builder $inner): Builder => $inner->where('is_customer', true)->orWhere('is_supplier', false),
                        ),
                        false: static fn (Builder $query): Builder => $query,
                        blank: static fn (Builder $query): Builder => $query,
                    ));
            },
        );
    }
}
