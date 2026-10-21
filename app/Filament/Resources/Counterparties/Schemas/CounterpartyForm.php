<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Counterparties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Utils\HasForm;
use Modules\CRM\Casts\CounterpartyKind;

final class CounterpartyForm
{
    use HasForm;

    public static function configure(Schema $schema): Schema
    {
        self::configureForm($schema);

        $kind_options = collect(CounterpartyKind::cases())
            ->mapWithKeys(static fn (CounterpartyKind $kind): array => [$kind->value => $kind->value])
            ->all();

        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('kind')
                    ->options($kind_options)
                    ->default(CounterpartyKind::Company->value)
                    ->required(),
                Select::make('parent_counterparty_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('tax_id')
                    ->maxLength(32),
                TextInput::make('vat_number')
                    ->maxLength(32),
                TextInput::make('fiscal_country')
                    ->length(2)
                    ->default('IT'),
                TextInput::make('address_line')
                    ->maxLength(255),
                TextInput::make('postal_code')
                    ->maxLength(16),
                TextInput::make('city')
                    ->maxLength(128),
                TextInput::make('province')
                    ->maxLength(8),
                TextInput::make('country')
                    ->length(2)
                    ->default('IT'),
                TextInput::make('einvoice_recipient_code')
                    ->maxLength(7),
                TextInput::make('einvoice_pec_email')
                    ->email()
                    ->maxLength(255),
            ]);
    }
}
