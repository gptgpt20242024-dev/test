<?php

namespace app\modules\process\services\data_item\identifier;

use app\modules\process\dto\data_item\DataItemDto;
use app\modules\process\services\data_item\DataItemLoaderInterface;
use app\modules\process\widgets\identifier\{BaseIdentifierInputWidget, BaseIdentifierViewWidget};

abstract class BaseIdentifier
{
    /**
     * Human readable name of identifier type
     */
    public static function getName(): string
    {
        return static::class;
    }

    /**
     * @return class-string<DataItemDto>
     */
    public static function getDtoClass(): string
    {
        return DataItemDto::class;
    }

    /**
     * @return class-string<DataItemLoaderInterface>
     */
    abstract public static function getLoaderClass(): string;

    /**
     * @return class-string<BaseIdentifierViewWidget>
     */
    public static function getViewWidgetClass(): string
    {
        return BaseIdentifierViewWidget::class;
    }

    /**
     * @return class-string<BaseIdentifierInputWidget>
     */
    public static function getInputWidgetClass(): string
    {
        return BaseIdentifierInputWidget::class;
    }
}
