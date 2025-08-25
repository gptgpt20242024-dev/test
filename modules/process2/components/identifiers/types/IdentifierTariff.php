<?php

namespace app\modules\process2\components\identifiers\type;

use app\modules\process\widgets\identifier\{TariffInputWidget, TariffViewWidget};
use app\modules\process2\components\identifiers\BaseIdentifier;
use app\modules\process2\dto\data\type\DataItemTariffDto;
use app\modules\process2\services\data\loader\type\TariffDataItemLoader;

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
