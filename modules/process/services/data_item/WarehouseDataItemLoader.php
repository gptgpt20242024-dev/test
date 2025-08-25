<?php

namespace app\modules\process\services\data_item;

use app\modules\process\dto\data_item\DataItemWarehouseDto;
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
