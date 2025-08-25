<?php

namespace app\modules\process2\services\data;

use app\modules\process2\components\identifier\BaseIdentifier;
use app\modules\process2\services\data\IdentifierConfigException;
use app\modules\process2\services\data\IdentifierMapValidator;

/**
 * Builds the final identifier map on first access, after all presets are registered.
 */
final class LazyFinalMapProvider implements IdentifierMapProvider
{
    /** @var array<int, class-string<BaseIdentifier>>|null */
    private ?array $map = null;

    /**
     * @param array<string>|string $includes Names of presets to include or '*' for all
     * @param array<int, class-string<BaseIdentifier>> $overrides Final overrides from application config
     */
    public function __construct(
        private IdentifierPresetRegistry $registry,
        private array|string $includes = '*',
        private array $overrides = [],
        private ?IdentifierMapValidator $validator = null,
    ) {
    }

    public function getMap(): array
    {
        if ($this->map !== null) {
            return $this->map;
        }

        $final  = [];
        $seenBy = [];

        $names = $this->includes === '*'
            ? array_keys($this->registry->all())
            : (array) $this->includes;

        foreach ($names as $name) {
            $map = $this->registry->get($name);
            if ($this->validator) {
                $this->validator->validatePreset($name, $map);
            }
            foreach ($map as $id => $class) {
                if (isset($final[$id])) {
                    $prev = $seenBy[$id] ?? 'unknown';
                    throw new IdentifierConfigException(
                        "Duplicate identifier id {$id} between '{$prev}' and '{$name}'"
                    );
                }
                $final[$id]  = $class;
                $seenBy[$id] = $name;
            }
        }

        foreach ($this->overrides as $id => $class) {
            $final[$id]  = $class;
            $seenBy[$id] = 'override';
        }

        if ($this->validator) {
            $this->validator->validateFinal($final);
        }

        return $this->map = $final;
    }
}
