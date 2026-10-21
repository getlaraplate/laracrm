<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Counterparties;

use BackedEnum;
use Coolsam\Modules\Resource;
use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Core\Filament\Utils\HasTranslatedResourceLabels;
use Modules\CRM\Filament\Resources\Counterparties\Pages\CreateCounterparty;
use Modules\CRM\Filament\Resources\Counterparties\Pages\EditCounterparty;
use Modules\CRM\Filament\Resources\Counterparties\Pages\ListCounterparties;
use Modules\CRM\Filament\Resources\Counterparties\RelationManagers\ContactsRelationManager;
use Modules\CRM\Filament\Resources\Counterparties\Schemas\CounterpartyForm;
use Modules\CRM\Filament\Resources\Counterparties\Tables\CounterpartiesTable;
use Modules\CRM\Models\Counterparty;
use Override;
use UnitEnum;

final class CounterpartyResource extends Resource
{
    use HasTranslatedResourceLabels;

    #[Override]
    protected static ?string $model = Counterparty::class;

    #[Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'crm.navigation.master_data';

    #[Override]
    protected static ?int $navigationSort = 10;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'crm/counterparties';
    }

    /**
     * @return array<int, string>
     */
    #[Override]
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'vat_number', 'tax_id'];
    }

    public static function form(Schema $schema): Schema
    {
        return CounterpartyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CounterpartiesTable::configure($table)->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [ContactsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCounterparties::route('/'),
            'create' => CreateCounterparty::route('/create'),
            'edit' => EditCounterparty::route('/{record}/edit'),
        ];
    }
}
