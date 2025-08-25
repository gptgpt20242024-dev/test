<?php

namespace app\modules\process2\dto\data\type;

use app\modules\process2\dto\data\DataItemDto;
use app\modules\utm\models\Tariffs;

class DataItemTariffDto extends DataItemDto
{
    public ?Tariffs $tariff = null;
}
