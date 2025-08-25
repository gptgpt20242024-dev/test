<?php

namespace app\modules\process\dto\data_item;

use app\modules\utm\models\ServicesData;

final class DataItemServiceDto extends DataItemDto
{
    public ?ServicesData $service = null;
}
