<?php

namespace app\modules\process\controllers;

use app\controllers\BaseController;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\widgets\identifier\IdentifierViewTypeWidget;
use app\modules\process2\factories\data\{DataItemDtoFactory};
use app\modules\process2\services\data\loader\DataItemBatchLoader;
use Yii;

class TaskDataTestController extends BaseController
{
    public function actionTest($taskId)
    {
        $models = Req3TasksDataItems::find()
            ->where(['link_id' => $taskId, 'link_type' => Req3TasksDataItems::LINK_TYPE_TASK])
            ->all();

        /** @var DataItemDtoFactory $factory */
        $factory = Yii::$container->get(DataItemDtoFactory::class);
        /** @var DataItemBatchLoader $batchLoader */
        $batchLoader = Yii::$container->get(DataItemBatchLoader::class);

        $dtos = [];
        foreach ($models as $model) {
            $dtos[] = $factory->create($model);
        }

        $batchLoader->load($dtos);

        $views = [];
        foreach ($dtos as $dto) {
            $views[] = IdentifierViewTypeWidget::widget(['item' => $dto]);
        }

        return $this->asJson($views);
    }
}
