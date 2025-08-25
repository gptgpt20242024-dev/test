<?php

namespace app\modules\process\services\data_item;

use app\modules\process\dto\data_item\DataItemDto;

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
