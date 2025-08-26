<?php

namespace app\modules\process2\data\services\loaders\types;

use app\modules\process2\data\dto\types\DataItemWarehouseDto;
use app\modules\process2\data\services\loaders\DataItemLoaderInterface;
use app\modules\warehouse\models\Warehouse;
use yii\helpers\ArrayHelper;

final class WarehouseDataItemLoader implements DataItemLoaderInterface
{
    /**
     * @param DataItemWarehouseDto[] $items
     */
    public function loadDetailData(array $items): void
    {
        $ids = array_filter(ArrayHelper::getColumn($items, 'valueId'));
        if (empty($ids)) {
            return;
        }
        $models = Warehouse::find()->where(['id' => $ids])->indexBy('id')->all();
        foreach ($items as $item) {
            $item->warehouse = $models[$item->valueId] ?? null;
        }
    }
}
