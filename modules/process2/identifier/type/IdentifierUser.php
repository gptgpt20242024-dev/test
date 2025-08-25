<?php

namespace app\modules\process2\identifier\type;

use app\modules\process2\identifier\BaseIdentifier;
use app\modules\process2\data\dto\type\DataItemUserDto;
use app\modules\process2\data\services\loader\type\UserDataItemLoader;

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
