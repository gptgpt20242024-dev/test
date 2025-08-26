<?php

namespace app\modules\process2\components\identifier\identifiers\types;

use app\modules\process2\components\data\dto\types\DataItemTariffDto;
use app\modules\process2\components\data\loaders\types\TariffDataItemLoader;
use app\modules\process2\components\data\widgets\edit\types\TariffInputWidget;
use app\modules\process2\components\data\widgets\view\types\TariffViewWidget;
use app\modules\process2\components\identifier\identifiers\BaseIdentifier;

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
