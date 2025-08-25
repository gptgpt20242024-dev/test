<?php

namespace app\modules\process2\dto\data\type;

use app\modules\process2\dto\data\DataItemDto;
use app\modules\warehouse\models\Warehouse;

class DataItemWarehouseDto extends DataItemDto
{
    public ?Warehouse $warehouse = null;
}
