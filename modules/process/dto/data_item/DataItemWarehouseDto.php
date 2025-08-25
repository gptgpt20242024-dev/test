<?php

namespace app\modules\process\dto\data_item;

use app\modules\warehouse\models\Warehouse;

final class DataItemWarehouseDto extends DataItemDto
{
    public ?Warehouse $warehouse = null;
}
