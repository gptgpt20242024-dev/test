<?php

namespace app\modules\process\services\data_item;

use app\modules\process\dto\data_item\DataItemUserDto;
use app\modules\user\models\Users;
use yii\helpers\ArrayHelper;

final class UserDataItemLoader implements DataItemLoaderInterface
{
    /**
     * @param DataItemUserDto[] $items
     */
    public function loadDetailData(array $items): void
    {
        $ids = array_filter(ArrayHelper::getColumn($items, 'valueId'));
        if (empty($ids)) {
            return;
        }
        $models = Users::find()->where(['id' => $ids])->indexBy('id')->all();
        foreach ($items as $item) {
            $item->user = $models[$item->valueId] ?? null;
        }
    }
}
