<?php

namespace app\modules\process2\data\services\loader;

use app\modules\process2\data\dto\DataItemDto;

interface DataItemLoaderInterface
{
    /**
     * @param DataItemDto[] $items
     */
    public function loadDetailData(array $items): void;
}
