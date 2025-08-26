<?php

namespace app\modules\process2\components\data\loaders;

use app\modules\process2\components\data\dto\DataItemDto;

interface DataItemLoaderInterface
{
    /**
     * @param DataItemDto[] $items
     */
    public function loadDetailData(array $items): void;
}
