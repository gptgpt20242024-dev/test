<?php

use app\modules\process2\identifier\types\IdentifierService;
use app\modules\process2\identifier\types\IdentifierTariff;
use app\modules\process2\identifier\types\IdentifierUser;

return [
    50 => IdentifierUser::class,
    60 => IdentifierTariff::class,
    61 => IdentifierService::class,
];
