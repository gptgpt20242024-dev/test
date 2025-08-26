<?php
namespace app\modules\process2\components\data\widgets\view\types;

use app\modules\process2\components\data\dto\types\DataItemTariffDto;
use app\modules\process2\components\data\widgets\view\BaseIdentifierViewWidget;

final class TariffViewWidget extends BaseIdentifierViewWidget
{
    public DataItemTariffDto $item;

    public function run(): string
    {
        $name = $this->item->tariff->name ?? ('#' . ($this->item->valueId ?? ''));
        return 'Tariff: ' . $name;
    }
}
