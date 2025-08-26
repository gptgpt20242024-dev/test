<?php
namespace app\modules\process2\components\data\widgets\edit;

use app\modules\process2\components\data\dto\DataItemDto;
use app\modules\process2\components\identifier\services\IdentifierRegistry;
use Yii;
use yii\base\Widget;

final class IdentifierInputTypeWidget extends Widget
{
    public DataItemDto $item;

    public function run(): string
    {
        /** @var IdentifierRegistry $registry */
        $registry = Yii::$container->get(IdentifierRegistry::class);
        $identifierClass = $registry->getClassByType($this->item->type);
        if ($identifierClass === null) {
            return '';
        }
        $widgetClass = $identifierClass::getInputWidgetClass();
        return Yii::createObject($widgetClass, ['item' => $this->item])->run();
    }
}
