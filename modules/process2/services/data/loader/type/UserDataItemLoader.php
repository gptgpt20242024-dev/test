<?php

namespace app\modules\process2\services\data\loader\type;

use app\modules\process2\dto\data\type\DataItemUserDto;
use app\modules\process2\services\data\loader\DataItemLoaderInterface;
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
