<?php

declare(strict_types=1);

namespace Modules\CRM\Services;

use Modules\Core\Services\Export\TabularCsvExporter;

/**
 * @phpstan-import-type PipelineReport from SalesPipelineService
 */
final readonly class SalesPipelineCsvExporter
{
    public function __construct(
        private TabularCsvExporter $csv_exporter,
    ) {}

    /**
     * @param  PipelineReport  $report
     */
    public function export(array $report): string
    {
        return $this->csv_exporter->export(
            columns: [
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'count', 'label' => 'Count'],
                ['key' => 'expected_value_doc', 'label' => 'Expected doc'],
                ['key' => 'expected_value_local', 'label' => 'Expected local'],
            ],
            rows: array_values($report['by_status']),
        );
    }
}
