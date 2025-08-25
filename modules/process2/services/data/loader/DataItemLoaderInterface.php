<?php

namespace app\modules\process2\services\data\loader;

use app\modules\process2\dto\data\DataItemDto;

interface DataItemLoaderInterface
{
    /**
     * @param DataItemDto[] $items
     */
    public function loadDetailData(array $items): void;
}
