<?php
namespace app\modules\process2\data\widgets\edit;

use app\modules\process2\data\dto\DataItemDto;
use yii\base\Widget;

abstract class BaseIdentifierInputWidget extends Widget
{
    public DataItemDto $item;
}
