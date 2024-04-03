<?php

use yii\widgets\Breadcrumbs;

echo Breadcrumbs::widget([
    'links' => $this->params['breadcrumbs'] ?? [],
]);
