<?php
use app\modules\process\services\data_item\identifier\{IdentifierUser, IdentifierService, IdentifierTariff};
return [
    'identifiers' => [
        50 => IdentifierUser::class,
        60 => IdentifierTariff::class,
        61 => IdentifierService::class,
    ],
];
