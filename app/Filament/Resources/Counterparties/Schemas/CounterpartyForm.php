<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Counterparties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
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
                Select::make('kind')
                    ->options($kind_options)
                    ->default(CounterpartyKind::Company->value)
                    ->live()
                    ->required(),
                TextInput::make('first_name')
                    ->maxLength(128)
                    ->live(onBlur: true)
                    ->visible(static fn (Get $get): bool => CounterpartyKind::Person->value === $get('kind')),
                TextInput::make('last_name')
                    ->maxLength(128)
                    ->live(onBlur: true)
                    ->visible(static fn (Get $get): bool => CounterpartyKind::Person->value === $get('kind')),
                TextInput::make('name')
                    ->required(static fn (Get $get): bool => CounterpartyKind::Person->value !== $get('kind')
                        || (blank($get('first_name')) && blank($get('last_name'))))
                    ->helperText(static fn (Get $get): ?string => CounterpartyKind::Person->value === $get('kind')
                        ? __('crm.help.personNameDerived')
                        : null)
                    ->maxLength(255),
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
