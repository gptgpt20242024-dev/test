<?php
namespace app\modules\process2\components\data\widgets\edit;

use app\modules\process2\components\data\dto\DataItemDto;
use yii\base\Widget;

abstract class BaseIdentifierInputWidget extends Widget
{
    public DataItemDto $item;
}
