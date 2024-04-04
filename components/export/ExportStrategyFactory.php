<?php
namespace app\components\export;

use InvalidArgumentException;

class ExportStrategyFactory
{
    public const CSV = 'csv';
    public const XLSX = 'xlsx';
    public const EXPORT_STRATEGIES = [
        self::CSV,
        self::XLSX
    ];

    public static function create(string $exportType): ExportStrategyInterface
    {
        return match (strtolower($exportType)) {
            self::CSV => new CsvExportStrategy(),
            self::XLSX => new XlsxExportStrategy(),
            default => throw new InvalidArgumentException(sprintf('Unsupported export type: %s', $exportType)),
        };
    }
}