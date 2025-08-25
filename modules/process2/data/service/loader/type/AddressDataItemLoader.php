<?php

namespace app\modules\process2\data\service\loader\type;

use app\modules\address\models\MapAddresses;
use app\modules\process2\data\dto\type\DataItemAddressDto;
use app\modules\process2\data\service\loader\DataItemLoaderInterface;
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
