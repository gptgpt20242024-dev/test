<?php

namespace app\modules\process2\components\identifier\identifiers\types;

use app\modules\process2\components\data\dto\types\DataItemServiceDto;
use app\modules\process2\components\data\loaders\types\ServiceDataItemLoader;
use app\modules\process2\components\data\widgets\edit\types\ServiceInputWidget;
use app\modules\process2\components\data\widgets\view\types\ServiceViewWidget;
use app\modules\process2\components\identifier\identifiers\BaseIdentifier;

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
