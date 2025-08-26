<?php

namespace app\modules\process2\components\data\dto\types;

use app\modules\process2\components\data\dto\DataItemDto;
use app\modules\user\models\Users;

class DataItemUserDto extends DataItemDto
{
    public ?Users $user = null;
}
