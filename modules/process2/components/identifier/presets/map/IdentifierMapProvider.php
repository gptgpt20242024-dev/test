<?php

namespace app\modules\process2\components\identifier\presets\map;

use app\modules\process2\components\identifier\identifiers\BaseIdentifier;

interface IdentifierMapProvider
{
    /**
     * @return array<int, class-string<BaseIdentifier>>
     */
    public function getMap(): array;
}
