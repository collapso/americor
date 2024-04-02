<?php
namespace app\dataProviders;

use Generator;
use yii\data\ActiveDataProvider;
use yii\db\BatchQueryResult;

class HistoryDataProvider extends ActiveDataProvider
{
    public int $batchSize = 100;

    public function getExportIterator(): Generator
    {
        $batch = $this->prepareModels();
        foreach ($batch as $models) {
            foreach ($models as $model) {
                yield $model;
            }
        }
    }

    public function prepareModels(): BatchQueryResult
    {
        $query = clone $this->query;
        return $query->batch($this->batchSize);
    }
}