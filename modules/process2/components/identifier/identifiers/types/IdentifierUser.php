<?php

namespace app\modules\process2\components\identifier\identifiers\types;

use app\modules\process2\components\data\dto\types\DataItemUserDto;
use app\modules\process2\components\data\loaders\types\UserDataItemLoader;
use app\modules\process2\components\identifier\identifiers\BaseIdentifier;

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
