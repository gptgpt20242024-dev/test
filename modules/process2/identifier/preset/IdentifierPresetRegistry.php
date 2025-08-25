<?php

namespace app\modules\process2\identifier\preset;

use app\modules\process2\identifier\BaseIdentifier;

final class IdentifierPresetRegistry
{
    /**
     * @var array<string, array<int, class-string<BaseIdentifier>>>
     */
    private array $presets = [];

    /**
     * @param array<int, class-string<BaseIdentifier>> $map
     */
    public function add(string $name, array $map): void
    {
        $this->presets[$name] = $map;
    }

    /**
     * @return array<int, class-string<BaseIdentifier>>
     */
    public function get(string $name): array
    {
        return $this->presets[$name] ?? [];
    }

    /**
     * @return array<string, array<int, class-string<BaseIdentifier>>>
     */
    public function all(): array
    {
        return $this->presets;
    }
}
