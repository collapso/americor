<?php

namespace app\widgets\HistoryList\handlers;

use app\models\History;

interface HistoryEventViewHandlerInterface
{
    public function supports(string $event): bool;
    public function handle(History $model): array;
}