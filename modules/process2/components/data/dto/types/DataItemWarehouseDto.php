<?php

namespace app\modules\process2\components\data\dto\types;

use app\modules\process2\components\data\dto\DataItemDto;
use app\modules\warehouse\models\Warehouse;

class DataItemWarehouseDto extends DataItemDto
{
    public ?Warehouse $warehouse = null;
}
