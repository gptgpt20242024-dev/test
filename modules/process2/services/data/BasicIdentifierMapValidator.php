<?php

namespace app\modules\process2\services\data;

use app\modules\process2\components\identifier\BaseIdentifier;

final class BasicIdentifierMapValidator implements IdentifierMapValidator
{
    public function __construct(private string $baseClass = BaseIdentifier::class)
    {
    }

    public function validatePreset(string $presetName, array $map): void
    {
        foreach ($map as $id => $class) {
            if (!is_int($id)) {
                throw new IdentifierConfigException(
                    "Preset {$presetName}: key must be int, got " . gettype($id)
                );
            }
            if (!is_string($class)) {
                throw new IdentifierConfigException(
                    "Preset {$presetName}: value must be class-string"
                );
            }
        }
    }

    public function validateFinal(array $finalMap): void
    {
        foreach ($finalMap as $id => $class) {
            if (!class_exists($class)) {
                throw new IdentifierConfigException(
                    "Final map: class not found for id {$id}: {$class}"
                );
            }
            if (!is_subclass_of($class, $this->baseClass)) {
                throw new IdentifierConfigException(
                    "Final map: {$class} must extend {$this->baseClass} (id {$id})"
                );
            }
        }
    }
}
