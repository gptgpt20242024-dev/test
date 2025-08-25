<?php

namespace app\modules\process2\dto\data\type;

use app\modules\address\models\MapAddresses;
use app\modules\process2\dto\data\DataItemDto;

class DataItemAddressDto extends DataItemDto
{
    public ?MapAddresses $address = null;
}
