<?php
namespace app\repositories;

use Generator;

interface ExportRepositoryInterface
{
    public function getExportDataGenerator(int $batchSize): Generator;
}