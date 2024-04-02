<?php
namespace app\tests\unit\components\export;

use app\components\export\CsvExportStrategy;
use PHPUnit\Framework\TestCase;

class CsvExportStrategyTest extends TestCase
{
    public function testGenerateStreamWithHeaders()
    {
        $data = (function() {
            yield ['row1col1', 'row1col2'];
            yield ['row2col1', 'row2col2'];
        })();

        $csvExportStrategy = new CsvExportStrategy();
        $stream = $csvExportStrategy->generateStream($data, ['header1', 'header2']);

        $this->assertIsResource($stream);

        $content = stream_get_contents($stream);
        $this->assertStringContainsString("header1,header2", $content);
        $this->assertStringContainsString("row1col1,row1col2", $content);
        $this->assertStringContainsString("row2col1,row2col2", $content);

        fclose($stream);
    }

    public function testGenerateStreamWithoutHeaders()
    {
        $data = (function() {
            yield ['row1col1', 'row1col2'];
        })();

        $csvExportStrategy = new CsvExportStrategy();
        $stream = $csvExportStrategy->generateStream($data);

        $this->assertIsResource($stream);

        $content = stream_get_contents($stream);
        $this->assertStringNotContainsString("header", $content);
        $this->assertStringContainsString("row1col1,row1col2", $content);

        fclose($stream);
    }

    public function testGenerateFilename()
    {
        $csvExportStrategy = new CsvExportStrategy();
        $filename = $csvExportStrategy->generateFilename();

        $expectedPattern = '/export_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}.csv/';
        $this->assertMatchesRegularExpression($expectedPattern, $filename);
    }

    public function testGetMimetype()
    {
        $csvExportStrategy = new CsvExportStrategy();
        $mimeType = $csvExportStrategy->getMimetype();

        $this->assertEquals('text/csv', $mimeType);
    }
}
