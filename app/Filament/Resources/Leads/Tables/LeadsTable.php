<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Leads\Tables;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Modules\Core\Filament\Utils\HasTable;
use Modules\CRM\Casts\LeadStatus;
use Modules\CRM\Models\Counterparty;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\OpportunityStage;
use Modules\CRM\Services\LeadConversionService;

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
            actions: static function (Collection $default_actions): void {
                $default_actions->prepend(self::convertAction());
            },
        );
    }

    public static function convertAction(): Action
    {
        return Action::make('convert')
            ->label(__('crm.actions.convert'))
            ->icon(Heroicon::OutlinedArrowRightCircle)
            ->visible(static fn (Lead $record): bool => ! in_array($record->status, [LeadStatus::Converted, LeadStatus::Disqualified], true))
            ->authorize(static fn (Lead $record): bool => auth()->user()?->can('update', $record) ?? false)
            ->schema(static fn (Lead $record): array => [
                Select::make('stage_taxonomy_id')
                    ->label(__('crm.fields.stage_taxonomy_id'))
                    ->options(static fn (): array => OpportunityStage::query()
                        ->get()
                        ->mapWithKeys(static fn (OpportunityStage $stage): array => [(int) $stage->getKey() => (string) $stage->name])
                        ->all())
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label(__('crm.fields.name'))
                    ->default($record->title)
                    ->maxLength(255),
                Select::make('counterparty_id')
                    ->label(__('crm.fields.counterparty_id'))
                    ->options(static fn (): array => Counterparty::query()->orderBy('name')->limit(200)->pluck('name', 'id')->all())
                    ->searchable()
                    ->required()
                    ->visible($record->counterparty_id === null),
            ])
            ->action(static function (Lead $record, array $data): void {
                $counterparty = isset($data['counterparty_id']) ? Counterparty::query()->find($data['counterparty_id']) : null;

                resolve(LeadConversionService::class)->convert($record, (int) $data['stage_taxonomy_id'], $data['name'] ?? null, $counterparty);

                Notification::make()->title(__('crm.messages.leadConverted'))->success()->send();
            });
    }
}
