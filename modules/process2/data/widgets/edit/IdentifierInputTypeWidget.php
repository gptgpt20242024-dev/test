<?php
namespace app\modules\process2\data\widgets\edit;

use app\modules\process2\data\dto\DataItemDto;
use app\modules\process2\data\services\DataItemIdentifierRegistry;
use Yii;
use yii\base\Widget;

final class IdentifierInputTypeWidget extends Widget
{
    public DataItemDto $item;

    public function run(): string
    {
        /** @var DataItemIdentifierRegistry $registry */
        $registry = Yii::$container->get(DataItemIdentifierRegistry::class);
        $identifierClass = $registry->getClassByType($this->item->type);
        if ($identifierClass === null) {
            return '';
        }
        $widgetClass = $identifierClass::getInputWidgetClass();
        return Yii::createObject($widgetClass, ['item' => $this->item])->run();
    }
}
