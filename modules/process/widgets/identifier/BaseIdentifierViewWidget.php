<?php
namespace app\modules\process\widgets\identifier;

use app\modules\process\dto\data_item\DataItemDto;
use yii\base\Widget;

abstract class BaseIdentifierViewWidget extends Widget
{
    public DataItemDto $item;
}
