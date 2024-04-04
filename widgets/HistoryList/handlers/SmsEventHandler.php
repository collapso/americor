<?php

namespace app\widgets\HistoryList\handlers;

use app\models\History;
use app\models\Sms;
use Yii;

class SmsEventHandler implements HistoryEventViewHandlerInterface
{
    private const ITEM_VIEW = '_item_common';

    private const EVENT_TYPES = [
        History::EVENT_INCOMING_SMS,
        History::EVENT_OUTGOING_SMS,
    ];

    public function handle(History $model): array
    {
        return [
            self::ITEM_VIEW,
            [
                'user' => $model->user,
                'body' => $model->getEventHtml(),
                'footerDatetime' => $model->ins_ts,
                'footer' => $model->sms->direction == Sms::DIRECTION_INCOMING ?
                    Yii::t('app', 'Incoming message from {number}', [
                        'number' => $model->sms->phone_from ?? ''
                    ]) : Yii::t('app', 'Sent message to {number}', [
                        'number' => $model->sms->phone_to ?? ''
                    ]),
                'iconIncome' => $model->sms->direction == Sms::DIRECTION_INCOMING,
                'iconClass' => 'icon-sms bg-dark-blue'
            ]
        ];
    }

    public function supports(string $event): bool
    {
        return in_array($event, self::EVENT_TYPES, true);
    }
}