<?php

namespace app\modules\process2\validators;

use app\modules\process2\components\identifier\BaseIdentifier;
use app\modules\process2\exceptions\IdentifierConfigException;

final class BasicIdentifierMapValidator implements IdentifierMapValidator
{
    private string $baseClass = BaseIdentifier::class;

    public function __construct(string $baseClass = BaseIdentifier::class)
    {
        $this->baseClass = $baseClass;
    }

    public function validatePreset(string $presetName, array $map): void
    {
        foreach ($map as $id => $class) {
            if (!is_int($id)) {
                throw new IdentifierConfigException(
                    "В пресете {$presetName}: ключ должен быть числом, а сейчас " . gettype($id)." ($id)"
                );
            }
            if (!is_string($class)) {
                throw new IdentifierConfigException(
                    "В пресете {$presetName}: значение класса должно быть строкой"
                );
            }
        }
    }

    public function validateFinal(array $finalMap): void
    {
        foreach ($finalMap as $id => $class) {
            if (!class_exists($class)) {
                throw new IdentifierConfigException(
                    "Финальная карта идентификаторов: класс не найден для ключа {$id}: {$class}"
                );
            }
            if (!is_subclass_of($class, $this->baseClass)) {
                throw new IdentifierConfigException(
                    "Финальная карта идентификаторов: {$class} должен наследоваться от {$this->baseClass} (id {$id})"
                );
            }
        }
    }
}
