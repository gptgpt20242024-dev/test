<?php

namespace app\modules\process2\components\data\loaders\types;

use app\modules\process2\components\data\dto\types\DataItemServiceDto;
use app\modules\process2\components\data\loaders\DataItemLoaderInterface;
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
