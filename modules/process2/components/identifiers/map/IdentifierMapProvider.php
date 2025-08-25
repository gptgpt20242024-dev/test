<?php

namespace app\modules\process2\components\identifiers\presets\map;

use app\modules\process2\components\identifiers\BaseIdentifier;

interface IdentifierMapProvider
{
    /**
     * @return array<int, class-string<BaseIdentifier>>
     */
    public function getMap(): array;
}
