<?php
namespace app\modules\process2\data\widgets\view;

use app\modules\process2\data\dto\DataItemDto;
use yii\base\Widget;

abstract class BaseIdentifierViewWidget extends Widget
{
    public DataItemDto $item;
}
