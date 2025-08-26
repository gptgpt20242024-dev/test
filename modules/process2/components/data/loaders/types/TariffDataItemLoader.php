<?php

namespace app\modules\process2\components\data\loaders\types;

use app\modules\process2\components\data\dto\types\DataItemTariffDto;
use app\modules\process2\components\data\loaders\DataItemLoaderInterface;
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
