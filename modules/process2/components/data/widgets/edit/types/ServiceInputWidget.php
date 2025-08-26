<?php
namespace app\modules\process2\components\data\widgets\edit\types;

use app\modules\process2\components\data\dto\types\DataItemServiceDto;
use app\modules\process2\components\data\widgets\edit\BaseIdentifierInputWidget;
use yii\helpers\Html;

final class ServiceInputWidget extends BaseIdentifierInputWidget
{
    public DataItemServiceDto $item;

    public function run(): string
    {
        $value = $this->item->valueId ?? '';
        return Html::input('text', 'service', $value);
    }
}
