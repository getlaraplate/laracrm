<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Resources\Leads;

use BackedEnum;
use Coolsam\Modules\Resource;
use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Utils\HasTranslatedResourceLabels;
use Modules\CRM\Filament\Resources\Leads\Pages\CreateLead;
use Modules\CRM\Filament\Resources\Leads\Pages\EditLead;
use Modules\CRM\Filament\Resources\Leads\Pages\ListLeads;
use Modules\CRM\Filament\Resources\Leads\Schemas\LeadForm;
use Modules\CRM\Filament\Resources\Leads\Tables\LeadsTable;
use Modules\CRM\Models\Lead;
use Override;
use UnitEnum;

final class LeadResource extends Resource
{
    use HasTranslatedResourceLabels;

    #[Override]
    protected static ?string $model = Lead::class;

    #[Override]
    protected static ?string $recordTitleAttribute = 'title';

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'crm.navigation.pipeline';

    #[Override]
    protected static ?int $navigationSort = 20;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'crm/leads';
    }

    public static function form(Schema $schema): Schema
    {
        return LeadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadsTable::configure($table)
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['counterparty', 'contact']))
            ->defaultSort('id', direction: 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
