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
        if (!empty($model->data_json)) {
            $items = json_decode($model->data_json, true) ?: [];
        }

        return $this->render('history', [
            'task' => $model,
            'items' => $items,
        ]);
    }
}
