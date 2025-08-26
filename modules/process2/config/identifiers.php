<?php

use app\modules\process2\components\identifier\identifiers\types\IdentifierService;
use app\modules\process2\components\identifier\identifiers\types\IdentifierTariff;
use app\modules\process2\components\identifier\identifiers\types\IdentifierUser;

return [
    50 => IdentifierUser::class,
    60 => IdentifierTariff::class,
    61 => IdentifierService::class,
];
