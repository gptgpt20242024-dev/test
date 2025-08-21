<?php

namespace app\modules\process\controllers;

use app\modules\process\models\task_archive\TaskArchive;
use app\modules\process\models\FormReq3SearchArchive;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

class TaskArchiveController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new FormReq3SearchArchive();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
