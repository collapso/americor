<?php

namespace app\widgets\HistoryList\handlers;

use app\models\History;
use Yii;
use yii\helpers\Html;

class FaxEventHandler implements HistoryEventViewHandlerInterface
{
    private const ITEM_VIEW = '_item_common';

    private const EVENT_TYPES = [
        History::EVENT_INCOMING_FAX,
        History::EVENT_OUTGOING_FAX,
    ];

    public function handle(History $model): array
    {
        $fax = $model->fax;

        // Can't find fax.document, fax.creditorGroup relations, but I'll keep this here
        $viewDocumentLink = isset($fax->document) ? Html::a(
            Yii::t('app', 'view document'),
            $fax->document->getViewUrl(),
            ['target' => '_blank', 'data-pjax' => 0]
        ) : '';

        $creditorGroup = isset($fax->creditorGroup)
            ? Html::a($fax->creditorGroup->name, ['creditors/groups'], ['data-pjax' => 0]) : '';

        $params = [
            'user' => $model->user,
            'footerDatetime' => $model->ins_ts,
            'body' => $model->getEventHtml() . ' - ' . $viewDocumentLink,
            'footer' => Yii::t('app', '{type} was sent to {group}', [
                'type' => $fax ? $fax->getTypeText() : 'Fax',
                'group' => $creditorGroup
            ]),
            'iconClass' => 'fa-fax bg-green'
        ];

        return [self::ITEM_VIEW, $params];
    }

    public function supports(string $event): bool
    {
        return in_array($event, self::EVENT_TYPES, true);
    }
}