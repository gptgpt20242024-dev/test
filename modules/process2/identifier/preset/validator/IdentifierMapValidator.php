<?php
namespace app\modules\process2\identifier\preset\validator;

use app\modules\process2\identifier\BaseIdentifier;

/**
 * @template T of BaseIdentifier
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
