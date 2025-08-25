<?php

namespace app\modules\process2\components\identifiers\types;

use app\modules\process\widgets\identifier\{ServiceInputWidget, ServiceViewWidget};
use app\modules\process2\components\identifiers\BaseIdentifier;
use app\modules\process2\dto\data\type\DataItemServiceDto;
use app\modules\process2\services\data\loader\type\ServiceDataItemLoader;

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
