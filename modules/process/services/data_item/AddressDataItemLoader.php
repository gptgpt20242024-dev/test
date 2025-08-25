<?php

namespace app\modules\process\services\data_item;

use app\modules\process\dto\data_item\DataItemAddressDto;
use app\modules\address\models\MapAddresses;
use yii\helpers\ArrayHelper;

final class AddressDataItemLoader implements DataItemLoaderInterface
{
    /**
     * @param DataItemAddressDto[] $items
     */
    public function loadDetailData(array $items): void
    {
        $ids = array_filter(ArrayHelper::getColumn($items, 'valueId'));
        if (empty($ids)) {
            return;
        }
        $models = MapAddresses::find()->where(['id' => $ids])->indexBy('id')->all();
        foreach ($items as $item) {
            $item->address = $models[$item->valueId] ?? null;
        }
    }
}
