<?php
use app\models\search\HistorySearch;
use app\widgets\HistoryList\handlers\HistoryEventViewHandler;
use app\widgets\HistoryList\handlers\CallEventHandler;
use app\widgets\HistoryList\handlers\CustomerChangeEventHandler;
use app\widgets\HistoryList\handlers\FaxEventHandler;
use app\widgets\HistoryList\handlers\SmsEventHandler;
use app\widgets\HistoryList\handlers\TaskEventHandler;

/** @var $model HistorySearch */

$historyEventHandler = new HistoryEventViewHandler([
    new CallEventHandler(),
    new CustomerChangeEventHandler(),
    new FaxEventHandler(),
    new SmsEventHandler(),
    new TaskEventHandler()
]);

[$viewName, $params] = $historyEventHandler->handle($model);
echo $this->render($viewName, $params);
