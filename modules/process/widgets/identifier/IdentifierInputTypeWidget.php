<?php
namespace app\modules\process\widgets\identifier;

use app\modules\process\dto\data_item\DataItemDto;
use app\modules\process\services\data_item\DataItemIdentifierRegistry;
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
