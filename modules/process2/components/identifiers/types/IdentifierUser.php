<?php

namespace app\modules\process2\components\identifiers\type;

use app\modules\process2\components\identifiers\BaseIdentifier;
use app\modules\process2\dto\data\type\DataItemUserDto;
use app\modules\process2\services\data\loader\type\UserDataItemLoader;

final class IdentifierUser extends BaseIdentifier
{
    public static function getName(): string
    {
        return 'User';
    }

    public static function getDtoClass(): string
    {
        return DataItemUserDto::class;
    }

    public static function getLoaderClass(): string
    {
        return UserDataItemLoader::class;
    }
}
