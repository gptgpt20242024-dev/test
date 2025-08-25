<?php

namespace app\modules\process2\dto\data\type;

use app\modules\process2\dto\data\DataItemDto;
use app\modules\user\models\Users;

class DataItemUserDto extends DataItemDto
{
    public ?Users $user = null;
}
