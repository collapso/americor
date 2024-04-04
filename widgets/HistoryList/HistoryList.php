<?php

namespace app\widgets\HistoryList;

use app\models\search\HistorySearch;
use yii\base\Widget;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
        $params = ArrayHelper::merge(
            [0 => 'history-export/export-data','exportType' => 'csv'],
            Yii::$app->request->queryParams
        );

        return $this->render('main', [
            'linkExport' => Url::to($params),
            'dataProvider' => (new HistorySearch())->search(Yii::$app->request->queryParams)
        ]);
    }
}
