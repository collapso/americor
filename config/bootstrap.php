<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/test.php';

// Create a Yii application instance to ensure Yii class and components are loaded
new yii\web\Application($config);