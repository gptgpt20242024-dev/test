<?php

namespace app\modules\process2\services\identifiers\map;

use app\modules\process2\components\identifier\BaseIdentifier;

interface IdentifierMapProvider
{
    /**
     * @return array<int, class-string<BaseIdentifier>>
     */
    public function getMap(): array;
}
