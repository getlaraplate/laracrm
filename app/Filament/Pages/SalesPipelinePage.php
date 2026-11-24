<?php

declare(strict_types=1);

namespace Modules\CRM\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Filament\Utils\HasPageForm;
use Modules\CRM\Services\SalesPipelineCsvExporter;
use Modules\CRM\Services\SalesPipelineService;
use Override;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

/**
 * @phpstan-import-type PipelineReport from SalesPipelineService
 */
final class SalesPipelinePage extends Page
{
    use HasPageForm;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * @var PipelineReport|array{}
     */
    public array $report_data = [];

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'crm.navigation.pipeline';

    #[Override]
    protected static ?int $navigationSort = 100;

    #[Override]
    protected static ?string $slug = 'sales-pipeline';

    #[Override]
    protected string $view = 'crm::filament.pages.sales-pipeline';

    #[Override]
    public static function getNavigationLabel(): string
    {
        return (string) __('crm.pages.salesPipeline');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                DatePicker::make('won_from'),
                DatePicker::make('won_to'),
            ]);
    }

    public function generate(): void
    {
        $this->report_data = $this->buildReport();
    }

    public function exportCsv(): StreamedResponse
    {
        $report = $this->report_data !== [] ? $this->report_data : $this->buildReport();
        $this->report_data = $report;

        $csv = resolve(SalesPipelineCsvExporter::class)->export($report);

        return response()->streamDownload(
            static function () use ($csv): void {
                echo $csv;
            },
            'sales-pipeline.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }

    #[Override]
    public function getTitle(): string
    {
        return (string) __('crm.pages.salesPipeline');
    }

    /**
     * @return PipelineReport
     */
    private function buildReport(): array
    {
        $state = $this->pageForm()->getState();
        $won_from = $state['won_from'] ?? null;
        $won_to = $state['won_to'] ?? null;

        return resolve(SalesPipelineService::class)->generate([
            'won_from' => is_string($won_from) ? $won_from : null,
            'won_to' => is_string($won_to) ? $won_to : null,
        ]);
    }
}
