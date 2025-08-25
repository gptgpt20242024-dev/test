<?php

use app\modules\process2\services\data\DataItemIdentifierRegistry;
use yii\helpers\ArrayHelper;

$di = [
    'singletons' => [
        DataItemIdentifierRegistry::class => DataItemIdentifierRegistry::class,
    ],
];

if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'di.local.php')) {
    $di = ArrayHelper::merge($di, require __DIR__ . DIRECTORY_SEPARATOR . 'di.local.php');
}

return $di;
