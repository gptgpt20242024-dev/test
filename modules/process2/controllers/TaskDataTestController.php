<?php

namespace app\modules\process2\controllers;

use app\controllers\BaseController;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process2\components\data\factories\DataItemDtoFactory;
use app\modules\process2\components\data\loaders\DataItemBatchLoader;
use app\modules\process2\components\data\widgets\view\IdentifierViewTypeWidget;
use Yii;
use yii\web\Controller;

class TaskDataTestController extends Controller
{
    public function actionTest($taskId)
    {
        $models = Req3TasksDataItems::find()
            ->where(['link_id' => $taskId, 'link_type' => Req3TasksDataItems::LINK_TYPE_TASK])
            ->all();

        /** @var DataItemDtoFactory $factory */
        $factory = Yii::$container->get(DataItemDtoFactory::class);
        $dtos = $factory->createAll($models);

        /** @var DataItemBatchLoader $batchLoader */
        $batchLoader = Yii::$container->get(DataItemBatchLoader::class);
        $batchLoader->load($dtos);

        $views = [];
        foreach ($dtos as $dto) {
            $views[] = IdentifierViewTypeWidget::widget(['item' => $dto]);
        }

        return $this->asJson($views);
    }
}
