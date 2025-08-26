<?php

namespace app\modules\process2\components\data\dto\types;

use app\modules\process2\components\data\dto\DataItemDto;
use app\modules\utm\models\ServicesData;

class DataItemServiceDto extends DataItemDto
{
    public ?ServicesData $service = null;
}
