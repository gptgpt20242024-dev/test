<?php

namespace app\modules\process\dto\data_item;

use app\modules\utm\models\Tariffs;

final class DataItemTariffDto extends DataItemDto
{
    public ?Tariffs $tariff = null;
}
