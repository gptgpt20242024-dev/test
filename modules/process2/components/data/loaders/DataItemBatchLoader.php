<?php

namespace app\modules\process2\components\data\loaders;

use app\modules\process2\components\data\dto\DataItemDto;
use app\modules\process2\components\identifier\services\IdentifierRegistry;

final class DataItemBatchLoader
{
    private IdentifierRegistry $registry;

    public function __construct(IdentifierRegistry $registry)
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
