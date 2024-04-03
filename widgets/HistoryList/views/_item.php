<?php
use app\models\Call;
use app\models\Customer;
use app\models\History;
use app\models\search\HistorySearch;
use app\models\Sms;
use yii\helpers\Html;

/** @var $model HistorySearch */

$params = [
    'user' => $model->user,
    'body' => $model->getEventHtml(),
    'footerDatetime' => $model->ins_ts,
    'iconClass' => 'icon-sms bg-dark-blue',
];

$viewName = '_item_common';

switch ($model->event) {
    case History::EVENT_CREATED_TASK:
    case History::EVENT_COMPLETED_TASK:
    case History::EVENT_UPDATED_TASK:
        $task = $model->task;
        array_merge($params, [
            'iconClass' => 'fa-check-square bg-yellow',
            'footer' => isset($task->customerCreditor->name) ? "Creditor: " . $task->customerCreditor->name : ''
        ]);
        break;
    case History::EVENT_INCOMING_SMS:
    case History::EVENT_OUTGOING_SMS:
        array_merge($params, [
            'footer' => $model->sms->direction == Sms::DIRECTION_INCOMING ?
                Yii::t('app', 'Incoming message from {number}', [
                    'number' => $model->sms->phone_from ?? ''
                ]) : Yii::t('app', 'Sent message to {number}', [
                    'number' => $model->sms->phone_to ?? ''
                ]),
            'iconIncome' => $model->sms->direction == Sms::DIRECTION_INCOMING,
            'iconClass' => 'icon-sms bg-dark-blue'
        ]);
        break;
    case History::EVENT_OUTGOING_FAX:
    case History::EVENT_INCOMING_FAX:
        $fax = $model->fax;
        // Can't find this fax.document relation, but I'll keep this here
        $viewDocumentLink = isset($fax->document) ? Html::a(
            Yii::t('app', 'view document'),
            $fax->document->getViewUrl(),
            ['target' => '_blank', 'data-pjax' => 0]
        ) : '';

        array_merge($params, [
            'body' => $model->getEventHtml() . ' - ' . $viewDocumentLink,
            'footer' => Yii::t('app', '{type} was sent to {group}', [
                'type' => $fax ? $fax->getTypeText() : 'Fax',
                'group' => isset($fax->creditorGroup) ? Html::a($fax->creditorGroup->name, ['creditors/groups'], ['data-pjax' => 0]) : ''
            ]),
            'iconClass' => 'fa-fax bg-green'
        ]);
        break;
    case History::EVENT_CUSTOMER_CHANGE_TYPE:
    case History::EVENT_CUSTOMER_CHANGE_QUALITY:
        $viewName = '_item_statuses_change'; // Different view for these events
        $params = array_merge($params, [
            'model' => $model,
            'oldValue' => $model->event == History::EVENT_CUSTOMER_CHANGE_TYPE
                ? Customer::getTypeTextByType($model->getDetailOldValue('type'))
                : Customer::getQualityTextByQuality($model->getDetailOldValue('quality')),
            'newValue' => $model->event == History::EVENT_CUSTOMER_CHANGE_TYPE
                ? Customer::getTypeTextByType($model->getDetailNewValue('type'))
                : Customer::getQualityTextByQuality($model->getDetailNewValue('quality')),
        ]);
        break;
    case History::EVENT_INCOMING_CALL:
    case History::EVENT_OUTGOING_CALL:
        /** @var Call $call */
        $call = $model->call;
        $answered = $call && $call->status == Call::STATUS_ANSWERED;
        array_merge($params, [
            'content' => $call->comment ?? '',
            'footer' => isset($call->applicant) ? "Called <span>{$call->applicant->name}</span>" : null,
            'iconClass' => $answered ? 'md-phone bg-green' : 'md-phone-missed bg-red',
            'iconIncome' => $answered && $call->direction == Call::DIRECTION_INCOMING
        ]);
        break;
    default:
        $params['iconClass'] = 'fa-gear bg-purple-light';
        break;
}

echo $this->render($viewName, $params);
