<?php

namespace app\modules\process2\services\identifiers\map;

use app\modules\process2\components\identifier\BaseIdentifier;
use app\modules\process2\exceptions\IdentifierConfigException;
use app\modules\process2\services\identifiers\IdentifierPresetRegistry;
use app\modules\process2\validators\IdentifierMapValidator;

/**
 * Builds the final identifier map on first access, after all presets are registered.
 */
final class LazyFinalMapProvider implements IdentifierMapProvider
{
    /** @var array<int, class-string<BaseIdentifier>>|null */
    private ?array                   $map       = null;
    private IdentifierPresetRegistry $registry;
    private                          $includes  = '*';
    private array                    $overrides = [];
    private ?IdentifierMapValidator  $validator = null;

    /**
     * @param array<string>|string $includes Names of presets to include or '*' for all
     * @param array<class-string<BaseIdentifier>, int> $overrides Final overrides from application config
     */
    public function __construct(
        IdentifierPresetRegistry $registry,
        $includes = '*',
        array $overrides = [],
        ?IdentifierMapValidator $validator = null
    ) {
        $this->validator = $validator;
        $this->overrides = $overrides;
        $this->includes = $includes;
        $this->registry = $registry;
    }

    public function getMap(): array
    {
        if ($this->map !== null) {
            return $this->map;
        }

        $finalIdentifiersMap  = [];
        $seenByPreset = [];

        $hasStar  = in_array('*', $this->includes);
        if ($hasStar) {
            $presetNames = array_keys($this->registry->all());
        } else {
            $presetNames = array_unique(array_filter($this->includes, fn($n) => $n !== '*'));
        }

        foreach ($presetNames as $presetName) {
            $identifiersMap = $this->registry->get($presetName);
            if (!$identifiersMap) {
                throw new IdentifierConfigException("Неизвестный пресет $presetName.");
            }

            if ($this->validator) {
                $this->validator->validatePreset($presetName, $identifiersMap);
            }

            foreach ($identifiersMap as $id => $class) {
                if (isset($this->overrides[$class])) {
                    $id = $this->overrides[$class];
                }

                if (isset($finalIdentifiersMap[$id])) {
                    $prevPreset = $seenByPreset[$id] ?? 'unknown';
                    throw new IdentifierConfigException(
                        "Дубль номера идентификатора: {$id} в '{$prevPreset}' и '{$presetName}'."
                    );
                }
                $finalIdentifiersMap[$id]  = $class;
                $seenByPreset[$id] = $presetName;
            }

        }

        if ($this->validator) {
            $this->validator->validateFinal($finalIdentifiersMap);
        }

        return $this->map = $finalIdentifiersMap;
    }

}
