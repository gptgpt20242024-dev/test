<?php

namespace app\modules\process2;

use app\modules\process\assets\ProcessAsset;
use app\modules\process2\services\data\DataItemIdentifierRegistry;
use app\modules\process2\services\data\IdentifierMapProvider;
use app\modules\process2\services\data\IdentifierPresetRegistry;
use app\modules\process2\services\data\LazyFinalMapProvider;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module;
use yii\web\Application as WebApplication;

class ProcessModule extends Module implements BootstrapInterface
{
    public function init()
    {
        parent::init();
        Yii::configure($this, require(__DIR__ . '/config/web.php'));
        Yii::configure(Yii::$container, require(__DIR__ . '/config/di.php'));

        if (Yii::$app instanceof WebApplication) {
            ProcessAsset::register(Yii::$app->view);
        }
    }

    public function bootstrap($app)
    {
        $c = Yii::$container;

        if (!$c->has(IdentifierPresetRegistry::class, true)) {
            $c->setSingleton(IdentifierPresetRegistry::class, IdentifierPresetRegistry::class);
        }

        $baseMap = (require __DIR__ . '/config/identifiers.php')['identifiers'] ?? [];
        $c->get(IdentifierPresetRegistry::class)->add('process2/base', $baseMap);

        if ($c->has(IdentifierMapProvider::class, true)) {
            return;
        }

        $includes = $app->params['process.identifiers.includes'] ?? '*';
        $overrides = $app->params['process.identifiers.map'] ?? [];

        $c->setSingleton(IdentifierMapProvider::class, function () use ($c, $includes, $overrides) {
            return new LazyFinalMapProvider(
                $c->get(IdentifierPresetRegistry::class),
                $includes,
                $overrides
            );
        });

        if (!$c->has(DataItemIdentifierRegistry::class, true)) {
            $c->setSingleton(DataItemIdentifierRegistry::class, DataItemIdentifierRegistry::class);
        }
    }
}
