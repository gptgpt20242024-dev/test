<?php
namespace app\modules\process\widgets\identifier;

use app\modules\process\dto\data_item\DataItemTariffDto;
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
