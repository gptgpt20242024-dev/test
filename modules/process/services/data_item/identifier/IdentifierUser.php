<?php

namespace app\modules\process\services\data_item\identifier;

use app\modules\process\dto\data_item\DataItemUserDto;
use app\modules\process\services\data_item\UserDataItemLoader;

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
