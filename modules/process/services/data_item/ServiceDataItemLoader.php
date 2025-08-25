<?php

namespace app\modules\process\services\data_item;

use app\modules\process\dto\data_item\DataItemServiceDto;
use app\modules\utm\models\ServicesData;
use yii\helpers\ArrayHelper;

final class ServiceDataItemLoader implements DataItemLoaderInterface
{
    /**
     * @param DataItemServiceDto[] $items
     */
    public function loadDetailData(array $items): void
    {
        $ids = array_filter(ArrayHelper::getColumn($items, 'valueId'));
        if (empty($ids)) {
            return;
        }
        $models = ServicesData::find()->where(['id' => $ids])->indexBy('id')->all();
        foreach ($items as $item) {
            $item->service = $models[$item->valueId] ?? null;
        }
    }
}
