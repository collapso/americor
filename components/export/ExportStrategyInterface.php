<?php
namespace app\components\export;

use Generator;

interface ExportStrategyInterface
{
    public function generateStream(Generator $data);
    public function getMimetype(): string;
    public function generateFilename(): string;
}