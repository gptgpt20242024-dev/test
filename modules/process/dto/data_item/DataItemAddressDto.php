<?php

namespace app\modules\process\dto\data_item;

use app\modules\address\models\MapAddresses;

final class DataItemAddressDto extends DataItemDto
{
    public ?MapAddresses $address = null;
}
