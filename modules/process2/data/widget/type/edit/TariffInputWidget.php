<?php
namespace app\modules\process2\data\widget\type\edit;

use app\modules\process2\data\dto\type\DataItemTariffDto;
use app\modules\process2\data\widget\BaseIdentifierInputWidget;
use yii\helpers\Html;

final class TariffInputWidget extends BaseIdentifierInputWidget
{
    public DataItemTariffDto $item;

    public function run(): string
    {
        $value = $this->item->valueId ?? '';
        return Html::input('text', 'tariff', $value);
    }
}
