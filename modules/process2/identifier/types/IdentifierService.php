<?php

namespace app\modules\process2\identifier\types;

use app\modules\process2\data\widgets\edit\types\ServiceInputWidget;
use app\modules\process2\data\widgets\view\types\ServiceViewWidget;
use app\modules\process2\identifier\BaseIdentifier;
use app\modules\process2\data\dto\types\DataItemServiceDto;
use app\modules\process2\data\services\loaders\types\ServiceDataItemLoader;

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
