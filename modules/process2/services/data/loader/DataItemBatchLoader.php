<?php

namespace app\modules\process2\services\data\loader;

use app\modules\process2\dto\data\DataItemDto;
use app\modules\process2\services\data\DataItemIdentifierRegistry;

final class DataItemBatchLoader
{
    public function __construct(private DataItemIdentifierRegistry $registry)
    {
    }

    /**
     * @param DataItemDto[] $items
     */
    public function load(array $items): void
    {
        $groups = [];
        foreach ($items as $item) {
            $groups[$item->type][] = $item;
        }
        foreach ($groups as $type => $group) {
            $loader = $this->registry->getLoader($type);
            if ($loader) {
                $loader->loadDetailData($group);
            }
        }
    }
}
