<?php

namespace app\modules\process\services\data_item;

use app\modules\process\dto\data_item\DataItemDto;

interface DataItemLoaderInterface
{
    /**
     * @param DataItemDto[] $items
     */
    public function loadDetailData(array $items): void;
}
