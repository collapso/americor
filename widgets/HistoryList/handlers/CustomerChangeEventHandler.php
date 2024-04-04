<?php

namespace app\widgets\HistoryList\handlers;

use app\models\Customer;
use app\models\History;

class CustomerChangeEventHandler implements HistoryEventViewHandlerInterface
{
    private const ITEM_VIEW = '_item_statuses_change';

    private const EVENT_TYPES = [
        History::EVENT_CUSTOMER_CHANGE_QUALITY,
        History::EVENT_CUSTOMER_CHANGE_TYPE,
    ];

    public function handle(History $model): array
    {
        return [
            self::ITEM_VIEW,
            [
                'model' => $model,
                'oldValue' => $model->event == History::EVENT_CUSTOMER_CHANGE_TYPE
                    ? Customer::getTypeTextByType($model->getDetailOldValue('type'))
                    : Customer::getQualityTextByQuality($model->getDetailOldValue('quality')),
                'newValue' => $model->event == History::EVENT_CUSTOMER_CHANGE_TYPE
                    ? Customer::getTypeTextByType($model->getDetailNewValue('type'))
                    : Customer::getQualityTextByQuality($model->getDetailNewValue('quality')),
            ]
        ];
    }

    public function supports(string $event): bool
    {
        return in_array($event, self::EVENT_TYPES, true);
    }
}