<?php

namespace app\modules\process2\identifier\presets\map;

use app\modules\process2\identifier\BaseIdentifier;

interface IdentifierMapProvider
{
    /**
     * @return array<int, class-string<BaseIdentifier>>
     */
    public function getMap(): array;
}
