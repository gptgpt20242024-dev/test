<?php

namespace app\modules\process2\identifier\type;

use app\modules\process2\data\widgets\edit\types\TariffInputWidget;
use app\modules\process2\data\widgets\view\types\TariffViewWidget;
use app\modules\process2\identifier\BaseIdentifier;
use app\modules\process2\data\dto\type\DataItemTariffDto;
use app\modules\process2\data\services\loader\type\TariffDataItemLoader;

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
