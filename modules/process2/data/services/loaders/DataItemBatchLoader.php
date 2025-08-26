<?php

namespace app\modules\process2\data\services\loaders;

use app\modules\process2\data\dto\DataItemDto;
use app\modules\process2\data\services\DataItemIdentifierRegistry;

final class DataItemBatchLoader
{
    private DataItemIdentifierRegistry $registry;

    public function __construct(DataItemIdentifierRegistry $registry)
    {
        $this->registry = $registry;
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
