<?php

use yii\helpers\ArrayHelper;

$config = [
    'params'              => [],
];

if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'web.local.php')) {
    $local_config = require(__DIR__ . DIRECTORY_SEPARATOR . 'web.local.php');
    $config = ArrayHelper::merge($config, $local_config);
}
return $config;
