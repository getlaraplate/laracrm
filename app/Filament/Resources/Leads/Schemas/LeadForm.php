<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Utils\HasForm;
use Modules\CRM\Casts\LeadStatus;

final class LeadForm
{
    use HasForm;

    public static function configure(Schema $schema): Schema
    {
        self::configureForm($schema);

        $status_options = collect(LeadStatus::cases())
            ->mapWithKeys(static fn (LeadStatus $s): array => [$s->value => $s->value])
            ->all();

        return $schema
            ->components([
                Select::make('counterparty_id')
                    ->relationship('counterparty', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('contact_id')
                    ->relationship('contact', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('source')
                    ->maxLength(128)
                    ->nullable(),
                Select::make('status')
                    ->options($status_options)
                    ->required()
                    ->default(LeadStatus::New->value),
                Select::make('owner_user_id')
                    ->relationship('owner', 'email')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Textarea::make('notes')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
                DateTimePicker::make('converted_at')
                    ->nullable(),
            ]);
    }
}
