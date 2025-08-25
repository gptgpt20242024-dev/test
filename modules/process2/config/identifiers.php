<?php

use app\modules\process2\components\identifiers\type\IdentifierService;
use app\modules\process2\components\identifiers\type\IdentifierTariff;
use app\modules\process2\components\identifiers\type\IdentifierUser;

return [
    50 => IdentifierUser::class,
    60 => IdentifierTariff::class,
    61 => IdentifierService::class,
];
