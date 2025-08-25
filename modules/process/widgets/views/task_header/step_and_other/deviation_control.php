<?php

use app\modules\process\models\task\Req3Tasks;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */

$info = $task->getDeviationInfo();
?>

<div>
    <div style="color: #884438">
        Всего отклонений по задаче: <b><?= $info['all'] ?></b> (шаг: <b><?= $info['steps'] ?></b> + маршрут: <b><?= $info['rules'] ?></b>)
    </div>
    <div class="form-group mb-0" style="margin-bottom: 10px">
        <label class="control-label">На какой шаг отправить задачу ?</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-dragon"></i>
                </div>
            </div>
            <?= Select2::widget([
                'id'            => "step_id",
                'name'          => "step_id",
                'value'         => null,
                'pluginOptions' => [
                    'data'       => $task->getStepsFromDeviationForSelect2(),
                    'allowClear' => true,
                ],
                'options'       => [
                    'placeholder' => "Выберите шаг",
                ]
            ]); ?>

            <div class="input-group-append">
                <button type="button" class="btn btn-dark" onclick="approveSendFormNextStep(this, <?= $task->id ?>)">
                    Применить
                </button>
            </div>
        </div>
    </div>
</div>