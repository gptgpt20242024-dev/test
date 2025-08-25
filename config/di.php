<?php
use app\modules\process\services\data_item\DataItemIdentifierRegistry;

return [
    'container' => [
        'singletons' => [
            DataItemIdentifierRegistry::class => DataItemIdentifierRegistry::class,
        ],
    ],
];
