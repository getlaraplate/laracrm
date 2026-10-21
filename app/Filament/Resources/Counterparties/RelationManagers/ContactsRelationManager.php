<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Counterparties\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\CRM\Models\ContactRole;
use Override;

/**
 * The contacts of a counterparty with the role each holds there and which one is the main contact.
 */
final class ContactsRelationManager extends RelationManager
{
    #[Override]
    protected static string $relationship = 'contacts';

    #[Override]
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email'),
                TextColumn::make('pivot.role.name')
                    ->label(__('crm.fields.role_taxonomy_id')),
                IconColumn::make('pivot.is_primary')
                    ->label(__('crm.fields.is_primary'))
                    ->boolean(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        ...self::pivotFields(),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema(self::pivotFields()),
                DetachAction::make(),
            ]);
    }

    /**
     * @return list<Select|Toggle>
     */
    private static function pivotFields(): array
    {
        return [
            Select::make('role_taxonomy_id')
                ->label(__('crm.fields.role_taxonomy_id'))
                ->options(static fn (): array => ContactRole::query()->get()->mapWithKeys(static fn (ContactRole $role): array => [(int) $role->getKey() => (string) $role->name])->all())
                ->searchable()
                ->nullable(),
            Toggle::make('is_primary')
                ->label(__('crm.fields.is_primary')),
        ];
    }
}
