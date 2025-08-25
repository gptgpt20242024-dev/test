<?php

use app\modules\communication\services\CommunicationService;
use app\modules\communication\services\LinkServiceInterface;
use app\modules\counterparties\services\CounterpartyService;
use yii\helpers\ArrayHelper;

$config = [
    'id'       => 'basic-console',
    'basePath' => dirname(__DIR__),

    'bootstrap' => [],

    'modules'             => [],
    'controllerNamespace' => 'app\commands',
    'components'          => [
            'cache'       => ['class' => 'yii\caching\FileCache'],
        ],
    'params'              => [],
];

return $config;
