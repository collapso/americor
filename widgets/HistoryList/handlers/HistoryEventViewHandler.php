<?php

namespace app\widgets\HistoryList\handlers;

use app\models\History;

class HistoryEventViewHandler
{
    /**
     * @param HistoryEventViewHandlerInterface[] $handlers
     */
    public function __construct(private array $handlers)
    {
    }

    public function handle(History $model): array
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($model->event)) {
                return $handler->handle($model);
            }
        }

        // Default
        return [
            '_item_common',
            [
                'user' => $model->user,
                'body' => $model->getEventHtml(),
                'footerDatetime' => $model->ins_ts,
                'iconClass' => 'icon-sms bg-dark-blue',
            ]
        ];
    }
}