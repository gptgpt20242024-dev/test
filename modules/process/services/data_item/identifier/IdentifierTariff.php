<?php

namespace app\modules\process\services\data_item\identifier;

use app\modules\process\dto\data_item\DataItemTariffDto;
use app\modules\process\services\data_item\TariffDataItemLoader;
use app\modules\process\widgets\identifier\{TariffInputWidget, TariffViewWidget};

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
