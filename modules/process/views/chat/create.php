<?php

use app\modules\process\models\FormChatCreate;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model FormChatCreate */
?>

<div data-create="1">
    <?php if ($model->hasErrors('task')): ?>
        <div class="alert alert-default-danger">
            Невозможно создать чат:
            <?php if ($model->hasErrors('task')): ?>
                <ul class="m-0">
                    <?php foreach ($model->getErrors('task') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php if ($model->task->active_chat): ?>
            <?= Html::button("Открыть текущий чат", ['class' => 'btn btn-light', 'onclick' => "showDialogChat({$model->task->active_chat->id})", 'data-dismiss' => 'modal']) ?>
        <?php endif; ?>
        <?php else: ?>
        <?php $form = ActiveForm::begin(['id' => 'chat_create']); ?>
        <?= $form->field($model, 'topic') ?>
        <?= $form->field($model, 'first_message')->textarea() ?>
            <script>autosize($('[id="<?=Html::getInputId($model, "first_message")?>"]'));</script>

    <?= $form->field($model, 'oper_ids')->widget(Select2::class, [
        'initValueText' => $model->getOperInitValueText(),
        'options'       => ['multiple' => true],
        'pluginOptions' => [
            'allowClear'         => true,
            'placeholder'        => 'Сделайте выбор',
            'minimumInputLength' => 2,
            'ajax'               => [
                'url'      => Url::toRoute(['/oper/json-select2-find-role']),
                'dataType' => 'json',
                'data'     => new JsExpression('function(params) { return {q:params.term' . (!empty($model->task->fm_id) ? (', fm_id: ' . $model->task->fm_id) : "") . '}; }'),
                'delay'    => 1000
            ],
            'escapeMarkup'       => new JsExpression('function (markup) { return markup; }'),
            'templateResult'     => new JsExpression("function (repo){ return repo.find;}"),
        ],
        'showToggleAll' => false
    ]); ?>

        <div class="js_errors" style="display: none">
            <div class="card" style="background-color: rgb(255, 215, 215);">
                <div class="body px-3 py-2 js_content">

                </div>
            </div>
        </div>

        <?php ActiveForm::end() ?>

    <?php endif; ?>
</div>