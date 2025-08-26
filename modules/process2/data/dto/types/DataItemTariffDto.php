<?php

namespace app\modules\process2\data\dto\types;

use app\modules\process2\data\dto\DataItemDto;
use app\modules\utm\models\Tariffs;

class DataItemTariffDto extends DataItemDto
{
    public ?Tariffs $tariff = null;
}
