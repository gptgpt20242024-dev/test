<?php

namespace app\modules\process2\dto\data\type;

use app\modules\process2\dto\data\DataItemDto;
use app\modules\utm\models\ServicesData;

class DataItemServiceDto extends DataItemDto
{
    public ?ServicesData $service = null;
}
