<?php

namespace app\widgets\HistoryList;

use app\components\HistorySearchService;
use app\models\search\HistorySearch;
use yii\base\Widget;
use Yii;

class HistoryList extends Widget
{
    public array $queryParams = [];

    public function init(): void
    {
        parent::init();
        // If there are no explicitly passed query parameters, use the request's query params
        if (empty($this->queryParams)) {
            $this->queryParams = Yii::$app->request->queryParams;
        }
    }

    public function run(): string
    {
        return $this->render('main', [
            'linkExport' => HistorySearchService::generateExportLink(Yii::$app->request->queryParams),
            'dataProvider' => (new HistorySearch())->search(Yii::$app->request->queryParams)
        ]);
    }
}
