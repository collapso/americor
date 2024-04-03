<?php
namespace app\components;

use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class HistorySearchService
{
    /**
     * @param string[] $queryParams
     */
    public static function generateExportLink(array $queryParams): string
    {
        $params = ArrayHelper::merge(['exportType' => 'csv'], $queryParams);
        $params[0] = 'history-export/export-data';
        return Url::to($params);
    }
}
