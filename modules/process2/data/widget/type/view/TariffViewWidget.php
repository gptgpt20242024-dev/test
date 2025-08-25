<?php
namespace app\modules\process2\data\widget\type\view;

use app\modules\process2\data\dto\type\DataItemTariffDto;
use app\modules\process2\data\widget\BaseIdentifierViewWidget;

final class TariffViewWidget extends BaseIdentifierViewWidget
{
    public DataItemTariffDto $item;

    public function run(): string
    {
        $name = $this->item->tariff->name ?? ('#' . ($this->item->valueId ?? ''));
        return 'Tariff: ' . $name;
    }
}
