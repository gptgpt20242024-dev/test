<?php

namespace app\modules\process2\services\data;

use app\modules\process2\components\identifier\BaseIdentifier;

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
            foreach ($this->registry->get($name) as $id => $class) {
                if (isset($final[$id])) {
                    $prev = $seenBy[$id] ?? 'unknown';
                    throw new \RuntimeException(
                        "Duplicate identifier id {$id} between presets '{$prev}' and '{$name}'."
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

        return $this->map = $final;
    }
}
