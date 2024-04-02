<?php

$config = require __DIR__ . '/web.php';

$config['id'] = 'americor';
$config['basePath'] = dirname(__DIR__);

$config['components']['db'] = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=americor-test', // Use a test database
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
];

// Disable the bootstrap for components that are not needed during tests or might cause side effects
unset($config['bootstrap']);

return $config;