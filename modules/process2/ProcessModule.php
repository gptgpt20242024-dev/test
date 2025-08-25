<?php

namespace app\modules\process2;

use app\modules\process2\components\identifier\BaseIdentifier;
use app\modules\process2\services\data\DataItemIdentifierRegistry;
use app\modules\process2\services\identifiers\IdentifierPresetRegistry;
use app\modules\process2\services\identifiers\map\IdentifierMapProvider;
use app\modules\process2\services\identifiers\map\LazyFinalMapProvider;
use app\modules\process2\validators\BasicIdentifierMapValidator;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module;

class ProcessModule extends Module implements BootstrapInterface
{
    public const PRESET_NAME = 'process2/base';

    /** @var string[]|'*' names of presets to include when building final map */
    public string|array $identifierIncludes = '*';

    /** @var array<int, class-string<BaseIdentifier>> final overrides */
    public array $identifierOverrides = [];

    public function init()
    {
        parent::init();
        Yii::configure($this, require(__DIR__ . '/config/web.php'));
    }

    public function bootstrap($app)
    {
        $c = Yii::$container;

        $c->get(IdentifierPresetRegistry::class)->add(self::PRESET_NAME, $this->loadPreset());

        if (!$c->has(IdentifierPresetRegistry::class)) {
            $c->setSingleton(IdentifierPresetRegistry::class, IdentifierPresetRegistry::class);
        }

        if (!$c->has(DataItemIdentifierRegistry::class)) {
            $c->setSingleton(DataItemIdentifierRegistry::class, DataItemIdentifierRegistry::class);
        }

        if (!$c->has(IdentifierMapProvider::class, true)) {
            $includes = $this->identifierIncludes;
            $overrides = $this->identifierOverrides;

            $c->setSingleton(IdentifierMapProvider::class, function () use ($c, $includes, $overrides) {
                return new LazyFinalMapProvider(
                    $c->get(IdentifierPresetRegistry::class),
                    $includes,
                    $overrides,
                    new BasicIdentifierMapValidator(BaseIdentifier::class)
                );
            });
        }
    }

    /**
     * @return array<int, class-string<BaseIdentifier>>
     */
    private function loadPreset(): array
    {
        return (require __DIR__ . '/config/identifiers.php') ?? [];
    }
}
