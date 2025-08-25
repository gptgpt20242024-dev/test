<?php
namespace app\modules\process\widgets\identifier;

use app\modules\process\dto\data_item\DataItemTariffDto;

final class TariffViewWidget extends BaseIdentifierViewWidget
{
    public DataItemTariffDto $item;

    public function run(): string
    {
        $name = $this->item->tariff->name ?? ('#' . ($this->item->valueId ?? ''));
        return 'Tariff: ' . $name;
    }
}
