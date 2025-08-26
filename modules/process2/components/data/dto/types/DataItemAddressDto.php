<?php

namespace app\modules\process2\components\data\dto\types;

use app\modules\address\models\MapAddresses;
use app\modules\process2\components\data\dto\DataItemDto;

class DataItemAddressDto extends DataItemDto
{
    public ?MapAddresses $address = null;
}
