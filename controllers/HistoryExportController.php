<?php

namespace app\controllers;

use app\components\export\ExportStrategyFactory;
use app\models\History;
use app\repositories\HistoryRepository;
use Exception;
use InvalidArgumentException;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class HistoryExportController extends Controller
{
    public function actionExportData(string $exportType = 'csv'): Response
    {
        try {
            $historyRepository = new HistoryRepository();
            $exportData = $historyRepository->getExportDataGenerator();
            $exportStrategy = ExportStrategyFactory::create($exportType);
            $stream = $exportStrategy->generateStream($exportData, History::EXPORT_HEADER_ROW);

            $response = Yii::$app->response;
            return $response->sendStreamAsFile($stream, $exportStrategy->generateFilename(), [
                'mimeType' => $exportStrategy->getMimetype(),
                'inline' => false
            ]);
        } catch (InvalidArgumentException | Exception $e) {
            Yii::$app->response->statusCode = 400;
            Yii::error("Error: " . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'An error occurred while generating the history export.');

            return $this->redirect(['index']);
        }
    }
}
