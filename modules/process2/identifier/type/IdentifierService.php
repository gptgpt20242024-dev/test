<?php

namespace app\modules\process2\identifier\type;

use app\modules\process2\data\widget\type\edit\ServiceInputWidget;
use app\modules\process2\data\widget\type\view\ServiceViewWidget;
use app\modules\process2\identifier\BaseIdentifier;
use app\modules\process2\data\dto\type\DataItemServiceDto;
use app\modules\process2\data\service\loader\type\ServiceDataItemLoader;

final class IdentifierService extends BaseIdentifier
{
    public static function getName(): string
    {
        return 'Service';
    }

    public static function getDtoClass(): string
    {
        return DataItemServiceDto::class;
    }

    public static function getLoaderClass(): string
    {
        return ServiceDataItemLoader::class;
    }

    public static function getViewWidgetClass(): string
    {
        return ServiceViewWidget::class;
    }

    public static function getInputWidgetClass(): string
    {
        return ServiceInputWidget::class;
    }
}
