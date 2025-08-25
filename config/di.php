<?php

use app\modules\process2\services\data\DataItemIdentifierRegistry;

return [
    'container' => [
        'singletons' => [
            DataItemIdentifierRegistry::class => DataItemIdentifierRegistry::class,
        ],
    ],
];
