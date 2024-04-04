<?php
namespace app\tests\unit\components\export;

use app\components\export\CsvExportStrategy;
use app\components\export\ExportStrategyFactory;
use app\components\export\XlsxExportStrategy;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ExportStrategyFactoryTest extends TestCase
{
    public function testCreateCsvExportStrategy()
    {
        $strategy = ExportStrategyFactory::create('csv');
        $this->assertInstanceOf(CsvExportStrategy::class, $strategy);
    }

    public function testCreateXlsxExportStrategy()
    {
        $strategy = ExportStrategyFactory::create('xlsx');
        $this->assertInstanceOf(XlsxExportStrategy::class, $strategy);
    }

    public function testCreateThrowsExceptionForUnsupportedType()
    {
        $this->expectException(InvalidArgumentException::class);
        ExportStrategyFactory::create('unsupported_type');
    }
}
