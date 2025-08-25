<?php
namespace app\modules\process\widgets\identifier;

use app\modules\process2\dto\data\type\DataItemServiceDto;
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
