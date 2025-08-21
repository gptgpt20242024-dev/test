<?php

namespace app\modules\process\controllers;

use app\controllers\BaseController;
use app\modules\process\models\FormReq3SearchArchive;
use app\modules\process\models\task_archive\TaskArchive;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class TaskArchiveController extends BaseController
{
    public function actionIndex()
    {
        $model = new FormReq3SearchArchive();
        $model->load(Yii::$app->request->get(), '');

        $query = $model->find();

        $pager = new Pagination();
        $pager->pageSize = 50;
        $pager->totalCount = $query->count();

        $query->limit($pager->limit);
        $query->offset($pager->offset);

        $tasks = $query->orderBy(['task_id' => SORT_DESC])->all();

        return $this->render('index', [
            'model' => $model,
            'tasks' => $tasks,
            'pager' => $pager,
        ]);
    }

    public function actionView($id)
    {
        $model = TaskArchive::find()->andWhere(['task_id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException('Архивная задача не найдена.');
        }

        $items = [];
        $timeExecute = null;
        $deviationInfo = [];
        $timeTemplate = null;

        if (!empty($model->data_json)) {
            $data = json_decode($model->data_json, true) ?: [];
            if (isset($data['items'])) {
                $items = $data['items'];
                $timeExecute = $data['time_execute'] ?? null;
                $deviationInfo = $data['deviation_info'] ?? [];
                $timeTemplate = $data['time_template'] ?? null;
            } else {
                $items = $data;
            }
        }

        return $this->render('view', [
            'task' => $model,
            'items' => $items,
            'timeExecute' => $timeExecute,
            'deviationInfo' => $deviationInfo,
            'timeTemplate' => $timeTemplate,
        ]);
    }
}
