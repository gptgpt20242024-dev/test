<?php

namespace app\modules\process2\services\data\loader\type;

use app\modules\process2\dto\data\type\DataItemTariffDto;
use app\modules\process2\services\data\loader\DataItemLoaderInterface;
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
