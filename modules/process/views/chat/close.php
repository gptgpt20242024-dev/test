<?php

use app\modules\lists\models\ListsGroups;
use app\modules\lists\models\ListsItems;
use app\modules\process\models\chats\Req3TasksChats;
use kartik\widgets\Select2;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $chat Req3TasksChats */
/* @var $list ListsGroups */
?>

<div data-close-list="1">
    <?php $form = ActiveForm::begin(['id' => 'chat_close']); ?>
    <div class="form-group">
        <label class="control-label">Выберите из списка причину:</label>
        <?= Select2::widget([
            'id'   => 'close_item_id',
            'name' => 'close_item_id',
            'value'         => null,
            'data'          => ListsItems::getSelect2List($list->id),
            'options'       => ['placeholder' => 'Список'],
            'pluginOptions' => ['allowClear' => true],
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