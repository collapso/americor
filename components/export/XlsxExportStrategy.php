<?php
namespace app\components\export;

use Generator;

class XlsxExportStrategy implements ExportStrategyInterface
{
    private const MIMETYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    public function generateStream(Generator $data)
    {
        // TODO: Implement export() method.
    }

    public function generateFilename(): string
    {
        // TODO: Implement generateFilename() method.
        return 'tmp';
    }

    public function getMimetype(): string
    {
        // TODO: Implement getMimetype() method.
        return self::MIMETYPE;
    }
}