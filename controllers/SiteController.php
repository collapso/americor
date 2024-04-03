<?php

namespace app\controllers;

use app\components\HistorySearchService;
use app\models\search\HistorySearch;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
