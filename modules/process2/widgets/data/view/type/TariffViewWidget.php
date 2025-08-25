<?php
namespace app\modules\process\widgets\identifier;

use app\modules\process2\dto\data\type\DataItemTariffDto;

final class TariffViewWidget extends BaseIdentifierViewWidget
{
    public DataItemTariffDto $item;

    public function run(): string
    {
        $name = $this->item->tariff->name ?? ('#' . ($this->item->valueId ?? ''));
        return 'Tariff: ' . $name;
    }
}
