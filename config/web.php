<?php

use app\modules\process2\components\identifiers\type\IdentifierUser;
use app\modules\process2\ProcessModule;

$config = [
    'language'  => 'ru',
    'id'        => 'basic',
    'basePath'  => dirname(__DIR__),
    'bootstrap' => [
        'process2',
    ],
    'aliases'   => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules'   => [
        'process2' => [
            'class'                    => app\modules\process2\ProcessModule::class,
            'identifierPresetIncludes' => [
                ProcessModule::PRESET_NAME
            ],
            'identifierOverrides'      => [
                IdentifierUser::class => 50,
            ],
        ],
    ],

    'components' => [],
    'params'     => [],
];

return $config;
