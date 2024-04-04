<?php
namespace app\repositories;

use app\dataProviders\HistoryDataProvider;
use app\models\History;
use Exception;
use Generator;

class HistoryRepository implements ExportRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function getExportDataGenerator(int $batchSize = 100): Generator
    {
        $query = (History::find())
            ->select(['history.id', 'history.ins_ts', 'history.event', 'history.object', 'history.object_id', 'history.detail', 'history.user_id'])
            ->with([
            'user' => function ($query) {
                $query->select(['user.id', 'user.username']);
            },
            'sms' => function ($query) {
                $query->select(['sms.id', 'sms.message']);
            },
            'task' => function ($query) {
                $query->select(['task.id', 'task.title']);
            },
            'call' => function ($query) {
                $query->select(['call.id', 'call.direction', 'call.status']);
            },
        ])->addOrderBy(['history.ins_ts' => SORT_DESC, 'history.id' => SORT_DESC]);

        $dataProvider = new HistoryDataProvider([
            'query' => $query,
            'batchSize' => 100,
        ]);

        /** @var History $model */
        foreach ($dataProvider->getExportIterator() as $model) {
            yield $model->getExportData();
        }
    }
}