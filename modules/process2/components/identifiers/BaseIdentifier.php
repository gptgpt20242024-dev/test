<?php

namespace app\modules\process2\components\identifiers;

use app\modules\process\widgets\identifier\{BaseIdentifierInputWidget, BaseIdentifierViewWidget};
use app\modules\process2\dto\data\DataItemDto;
use app\modules\process2\services\data\loader\DataItemLoaderInterface;

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
