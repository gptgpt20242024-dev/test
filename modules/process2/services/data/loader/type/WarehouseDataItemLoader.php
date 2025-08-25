<?php

namespace app\modules\process2\services\data\loader\type;

use app\modules\process2\dto\data\type\DataItemWarehouseDto;
use app\modules\process2\services\data\loader\DataItemLoaderInterface;
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
