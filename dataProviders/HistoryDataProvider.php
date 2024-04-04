<?php
namespace app\dataProviders;

use app\models\search\HistorySearch;
use Generator;
use yii\data\ActiveDataProvider;

class HistoryDataProvider extends ActiveDataProvider
{
    public int $batchSize = 100;

    public function getExportIterator(): Generator
    {
        $query = clone $this->query;
        $batch = $query->batch($this->batchSize);
        foreach ($batch as $models) {
            foreach ($models as $model) {
                yield $model;
            }
        }
    }
}