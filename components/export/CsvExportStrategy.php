<?php
namespace app\components\export;

use Generator;

class CsvExportStrategy implements ExportStrategyInterface
{
    private const MIMETYPE = 'text/csv';

    /**
     * @param string[] $headers
     * @return false|resource
     */
    public function generateStream(Generator $data, array $headers = [])
    {
        $stream = fopen('php://temp', 'w+b');

        if (!empty($headers)) {
            fputcsv($stream, $headers);
        }

        while ($data->valid()) {
            fputcsv($stream, $data->current());
            $data->next();
        }
        rewind($stream);

        return $stream;
    }

    public function generateFilename(): string
    {
        return sprintf('export_%s.csv', date('Y-m-d_H-i-s'));
    }

    public function getMimetype(): string
    {
        return self::MIMETYPE;
    }
}