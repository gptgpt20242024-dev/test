<?php

namespace app\modules\process2\services\data;

use app\modules\process2\components\identifiers\BaseIdentifier;
use app\modules\process2\components\identifiers\presets\map\IdentifierMapProvider;
use app\modules\process2\dto\data\DataItemDto;
use app\modules\process2\services\data\loader\DataItemLoaderInterface;
use Yii;


class DataItemIdentifierRegistry
{
    /** @var array<int, class-string<BaseIdentifier>> */
    private array $map;

    public function __construct(IdentifierMapProvider $provider)
    {
        $this->map = $provider->getMap();
    }

    /** @var array<string, DataItemLoaderInterface> */
    private array $loaderInstances = [];


    /**
     * @param int $type
     * @return class-string<BaseIdentifier>
     */
    public function getClassByType(int $type): ?string
    {
        return $this->map[$type] ?? null;
    }

    public function getTypeByClass(string $class): ?int
    {
        $type = array_search($class, $this->map, true);
        return $type === false ? null : $type;
    }

    public function getName(int $type): ?string
    {
        $class = $this->getClassByType($type);
        return $class ? $class::getName() : null;
    }

    /**
     * @return class-string<DataItemDto>
     */
    public function getDtoClass(int $type): string
    {
        $class = $this->getClassByType($type);
        return $class ? $class::getDtoClass() : DataItemDto::class;
    }

    public function getLoader(int $type): ?DataItemLoaderInterface
    {
        $class = $this->getClassByType($type);
        if ($class === null) {
            return null;
        }
        $loaderClass = $class::getLoaderClass();
        if (!isset($this->loaderInstances[$loaderClass])) {
            $this->loaderInstances[$loaderClass] = Yii::createObject($loaderClass);
        }

        return $this->loaderInstances[$loaderClass];
    }
}
