<?php

namespace app\modules\process2\identifier\types;

use app\modules\process2\data\widgets\edit\types\TariffInputWidget;
use app\modules\process2\data\widgets\view\types\TariffViewWidget;
use app\modules\process2\identifier\BaseIdentifier;
use app\modules\process2\data\dto\types\DataItemTariffDto;
use app\modules\process2\data\services\loaders\types\TariffDataItemLoader;

final class IdentifierTariff extends BaseIdentifier
{
    public static function getName(): string
    {
        return 'Tariff';
    }

    public static function getDtoClass(): string
    {
        return DataItemTariffDto::class;
    }

    public static function getLoaderClass(): string
    {
        return TariffDataItemLoader::class;
    }

    public static function getViewWidgetClass(): string
    {
        return TariffViewWidget::class;
    }

    public static function getInputWidgetClass(): string
    {
        return TariffInputWidget::class;
    }
}
