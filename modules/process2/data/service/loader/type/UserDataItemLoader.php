<?php

namespace app\modules\process2\data\service\loader\type;

use app\modules\process2\data\dto\type\DataItemUserDto;
use app\modules\process2\data\service\loader\DataItemLoaderInterface;
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
