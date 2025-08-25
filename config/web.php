<?php

use app\modules\communication\services\CommunicationService;
use app\modules\communication\services\LinkServiceInterface;
use app\modules\communication\widgets\WidgetFormChannel;
use app\modules\communication\widgets\WidgetFormChannels;
use app\modules\communication\widgets\WidgetViewChannels;
use app\modules\communication\widgets\WidgetViewGroupChannels;
use app\modules\counterparties\services\CounterpartyService;
use app\modules\user\services\UserService;
use yii\helpers\ArrayHelper;
use yii\rbac\ManagerInterface;

$config = [
    'language'     => 'ru',
    'id'           => 'basic',
    'basePath'     => dirname(__DIR__),
    'bootstrap'    => [],
    'aliases'      => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules'      => [],
    'components'   => [],
    'params'       => [],
];

return $config;
