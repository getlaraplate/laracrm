<?php

declare(strict_types=1);

namespace Modules\CRM\Services;

use Modules\Core\Services\Export\TabularCsvExporter;

final readonly class SalesPipelineCsvExporter
{
    public function __construct(
        private TabularCsvExporter $csv_exporter,
    ) {}

    /**
     * @param  array{
     *     by_status: array<string, array{status: string, count: int, expected_value_doc: string, expected_value_local: string}>,
     *     total_count: int,
     *     won_count: int,
     *     lost_count: int,
     *     won_value_doc: string,
     *     won_value_local: string,
     *     total_expected_value_doc: string,
     *     total_expected_value_local: string,
     * }  $report
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
