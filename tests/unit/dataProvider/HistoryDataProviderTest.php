<?php

namespace tests\unit\dataProvider;

use ArrayIterator;
use app\dataProviders\HistoryDataProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use yii\db\Query;

class HistoryDataProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @group dataProvider
     */
    public function testGetExportIterator()
    {
        $batchSize = 100;
        $data = range(1, $batchSize);

        $queryProphecy = $this->prophesize(Query::class);

        // Since we can't easily mock BatchQueryResult, we instead ensure our Query mock
        // will use an ArrayIterator to mimic the behavior of yielding batches of data.
        // Note: We're simplifying by assuming each batch is just an array of data for this example.
        $queryProphecy->batch($batchSize, Argument::any())->willReturn(new ArrayIterator([$data]));
        $queryMock = $queryProphecy->reveal();

        $provider = new HistoryDataProvider([
            'query' => $queryMock,
            'batchSize' => $batchSize,
        ]);

        $output = iterator_to_array($provider->getExportIterator(), false);

        $this->assertEquals($data, $output);
        $queryProphecy->batch($batchSize, Argument::any())->shouldHaveBeenCalled();
    }
}
