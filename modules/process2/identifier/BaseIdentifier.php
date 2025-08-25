<?php

namespace app\modules\process2\identifier;

use app\modules\process2\data\widget\{BaseIdentifierInputWidget, BaseIdentifierViewWidget};
use app\modules\process2\data\dto\DataItemDto;
use app\modules\process2\data\service\loader\DataItemLoaderInterface;

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
