<?php

use app\modules\lists\models\ListsGroups;
use app\modules\process\models\chats\Req3TasksChats;
use kartik\widgets\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $chat Req3TasksChats */
/* @var $list ListsGroups */
?>

<div data-invite="1">
    <?php $form = ActiveForm::begin(['id' => 'chat_invite']); ?>

    <div class="form-group">
        <label class="control-label">Выберите кого вы хотите призвать в чат:</label>
        <?= Select2::widget([
            'id'            => 'oper_ids',
            'name'          => 'oper_ids',
            'value'         => [],
            'options'       => ['multiple' => true],
            'pluginOptions' => [
                'allowClear'         => true,
                'placeholder'        => 'Сделайте выбор',
                'minimumInputLength' => 2,
                'ajax'               => [
                    'url'      => Url::toRoute(['/oper/json-select2-find-role']),
                    'dataType' => 'json',
                    'data'     => new JsExpression('function(params) { return {q:params.term' . (!empty($chat->task->fm_id) ? (', fm_id: ' . $chat->task->fm_id) : "") . '}; }'),
                    'delay'    => 1000
                ],
                'escapeMarkup'       => new JsExpression('function (markup) { return markup; }'),
                'templateResult'     => new JsExpression("function (repo){ return repo.find;}"),
            ],
            'showToggleAll' => false
        ]); ?>
    </div>

    <div class="js_errors" style="display: none">
        <div class="card" style="background-color: rgb(255, 215, 215);">
            <div class="body px-3 py-2 js_content">

            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>