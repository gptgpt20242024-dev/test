<?php

namespace app\modules\process2\components\identifier\identifiers;

use app\modules\process2\components\data\dto\DataItemDto;
use app\modules\process2\components\data\loaders\DataItemLoaderInterface;
use app\modules\process2\components\data\widgets\edit\BaseIdentifierInputWidget;
use app\modules\process2\components\data\widgets\view\BaseIdentifierViewWidget;

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
