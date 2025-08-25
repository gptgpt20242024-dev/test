<?php

namespace app\modules\process\dto\data_item;

use app\modules\user\models\Users;

final class DataItemUserDto extends DataItemDto
{
    public ?Users $user = null;
}
