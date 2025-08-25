<?php

namespace app\modules\process\services\data_item;

use app\modules\process\dto\data_item\DataItemTariffDto;
use app\modules\utm\models\Tariffs;
use yii\helpers\ArrayHelper;

final class TariffDataItemLoader implements DataItemLoaderInterface
{
    /**
     * @param DataItemTariffDto[] $items
     */
    public function loadDetailData(array $items): void
    {
        $ids = array_filter(ArrayHelper::getColumn($items, 'valueId'));
        if (empty($ids)) {
            return;
        }
        $models = Tariffs::find()->where(['id' => $ids])->indexBy('id')->all();
        foreach ($items as $item) {
            $item->tariff = $models[$item->valueId] ?? null;
        }
    }
}
