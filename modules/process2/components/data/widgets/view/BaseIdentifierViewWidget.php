<?php
namespace app\modules\process2\components\data\widgets\view;

use app\modules\process2\components\data\dto\DataItemDto;
use yii\base\Widget;

abstract class BaseIdentifierViewWidget extends Widget
{
    public DataItemDto $item;
}
