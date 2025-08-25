<?php

namespace app\modules\process2\validators;

/**
 * @template T of \app\modules\process2\components\identifier\BaseIdentifier
 */
interface IdentifierMapValidator
{
    /**
     * @param array<int, class-string> $map
     */
    public function validatePreset(string $presetName, array $map): void;

    /**
     * @param array<int, class-string> $finalMap
     */
    public function validateFinal(array $finalMap): void;
}
