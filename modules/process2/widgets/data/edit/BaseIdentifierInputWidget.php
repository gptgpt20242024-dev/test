<?php
namespace app\modules\process\widgets\identifier;

use app\modules\process2\dto\data\DataItemDto;
use yii\base\Widget;

abstract class BaseIdentifierInputWidget extends Widget
{
    public DataItemDto $item;
}
