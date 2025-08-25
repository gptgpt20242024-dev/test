<?php

namespace app\modules\process2\data\dto\type;

use app\modules\process2\data\dto\DataItemDto;
use app\modules\utm\models\ServicesData;

class DataItemServiceDto extends DataItemDto
{
    public ?ServicesData $service = null;
}
