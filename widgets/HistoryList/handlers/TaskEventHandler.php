<?php

namespace app\widgets\HistoryList\handlers;

use app\models\History;

class TaskEventHandler implements HistoryEventViewHandlerInterface
{
    private const ITEM_VIEW = '_item_common';

    private const EVENT_TYPES = [
        History::EVENT_CREATED_TASK,
        History::EVENT_COMPLETED_TASK,
        History::EVENT_UPDATED_TASK,
    ];

    public function handle(History $model): array
    {
        $task = $model->task;
        return [
            self::ITEM_VIEW,
            [
                'user' => $model->user,
                'body' => $model->getEventHtml(),
                'footerDatetime' => $model->ins_ts,
                'iconClass' => 'fa-check-square bg-yellow',
                'footer' => isset($task->customerCreditor->name) ? "Creditor: " . $task->customerCreditor->name : ''
            ]
        ];
    }

    public function supports(string $event): bool
    {
        return in_array($event, self::EVENT_TYPES, true);
    }
}