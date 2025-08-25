<?php

namespace app\modules\process2\data\dto\type;

use app\modules\process2\data\dto\DataItemDto;
use app\modules\warehouse\models\Warehouse;

class DataItemWarehouseDto extends DataItemDto
{
    public ?Warehouse $warehouse = null;
}
