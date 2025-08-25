<?php

namespace app\modules\process\services\data_item\identifier;

use app\modules\process\dto\data_item\DataItemServiceDto;
use app\modules\process\services\data_item\ServiceDataItemLoader;
use app\modules\process\widgets\identifier\{ServiceInputWidget, ServiceViewWidget};

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
