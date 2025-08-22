<?php

namespace app\modules\process\controllers;

use app\controllers\BaseController;
use app\modules\process\models\FormReq3SearchArchive;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task_archive\TaskArchive;
use app\modules\process\models\task_data\Req3TasksDataItems;
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
        $dataItems = [];
        $identifiers = [];

        if (!empty($model->data_json)) {
            $data = json_decode($model->data_json, true) ?: [];
            $items = $data['history']??[];
            $timeExecute = $data['time_execute'] ?? null;
            $deviationInfo = $data['deviation_info'] ?? [];
            $timeTemplate = $data['time_template'] ?? null;

            if (!empty($data['data_items']) && is_array($data['data_items'])) {
                $ids = [];
                foreach ($data['data_items'] as $row) {
                    $item = new Req3TasksDataItems([
                        'id'            => $row['id'] ?? null,
                        'identifier_id' => $row['identifier_id'] ?? null,
                        'type'          => $row['type'] ?? null,
                        'value_id'      => $row['value_id'] ?? null,
                        'value_text'    => $row['value_text'] ?? null,
                        'value_number'  => $row['value_number'] ?? null,
                        'oper_id'       => $row['oper_id'] ?? null,
                    ]);
                    $dataItems[$item->identifier_id][] = $item;
                    $ids[$item->identifier_id] = $item->identifier_id;
                }
                if (!empty($ids)) {
                    $identifiers = Req3Identifiers::find()->where(['id' => array_keys($ids)])->indexBy('id')->all();
                }
            }
        }

        return $this->render('view', [
            'task' => $model,
            'items' => $items,
            'timeExecute' => $timeExecute,
            'deviationInfo' => $deviationInfo,
            'timeTemplate' => $timeTemplate,
            'dataItems' => $dataItems,
            'identifiers' => $identifiers,
        ]);
    }
}
