<?php

use app\modules\process2\components\identifier\type\IdentifierService;
use app\modules\process2\components\identifier\type\IdentifierTariff;
use app\modules\process2\components\identifier\type\IdentifierUser;

return [
    50 => IdentifierUser::class,
    60 => IdentifierTariff::class,
    61 => IdentifierService::class,
];
