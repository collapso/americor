<?php

namespace app\widgets\HistoryList\handlers;

use app\models\Call;
use app\models\History;

class CallEventHandler implements HistoryEventViewHandlerInterface
{
    private const ITEM_VIEW = '_item_common';

    private const EVENT_TYPES = [
        History::EVENT_INCOMING_CALL,
        History::EVENT_OUTGOING_CALL,
    ];

    public function handle(History $model): array
    {
        $call = $model->call;
        $answered = $call && $call->status == Call::STATUS_ANSWERED;
        $params = [
            'user' => $model->user,
            'body' => $model->getEventHtml(),
            'footerDatetime' => $model->ins_ts,
            'content' => $call->comment ?? '',
            'footer' => isset($call->applicant) ? "Called <span>{$call->applicant->name}</span>" : null,
            'iconClass' => $answered ? 'md-phone bg-green' : 'md-phone-missed bg-red',
            'iconIncome' => $answered && $call->direction == Call::DIRECTION_INCOMING
        ];

        return [self::ITEM_VIEW, $params];
    }

    public function supports(string $event): bool
    {
        return in_array($event, self::EVENT_TYPES, true);
    }
}