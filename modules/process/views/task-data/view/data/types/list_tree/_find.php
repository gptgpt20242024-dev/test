<?php

use app\modules\lists\assets\ListAsset;
use app\modules\lists\models\ListsTreeGroups;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $text string */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $group ListsTreeGroups */
/* @var $data array */
/* @var $add_new boolean */

ListAsset::register($this);

$value = new Req3TasksDataItems();
$key = "i_" . $identifier->id;
?>


<div class="tree3">
    <ul class="tree3_item">
        <?php foreach ($data as $data_item): ?>
            <?= $this->render('_find_item', ['task' => $task, 'identifier' => $identifier, 'group' => $group, 'data_item' => $data_item, 'value' => $value, 'key' => $key]); ?>
        <?php endforeach; ?>
    </ul>

    <?php if ($group->is_complemented): ?>
        <button type="button" class="btn btn-success btn-sm" data-btn-add="1"><i class="fas fa-plus" style="color: #10e40c;"></i> Добавить</button>
        <span style="color: #7f7f7f; font-style: italic">"<?= $text ?>"</span> как новый элемент в дерево ?


        <div style="display: none" data-form-add="1">
            <input name="<?= Html::getInputName($value, '[' . $key . ']value_id') ?>" type="hidden" disabled value="<?= Html::encode($text) ?>" data-input-value="1"/>
            <?php $name = Html::getInputName($value, "[{$key}]comment"); ?>
            <?php $id = Html::getInputIdByName($name); ?>
            <div class="form-group">
                <label class="has-star" for="<?= $id ?>">Причина добавления</label>
                <input type="text" id="<?= $id ?>" class="form-control" name="<?= $name ?>" placeholder="Введите комментарий" data-input-comment-add="1">
            </div>


            <div style="display: flex; gap: 10px">
                <button type="button" class="btn btn-success btn-sm" data-btn-save="1"><i class="fas fa-plus" style="color: #10e40c;"></i> Добавить</button>
                <button type="button" class="btn btn-secondary btn-sm" data-btn-close="1">Отмена</button>
            </div>
        </div>

        <script>
            $(function () {
                let $container = $("#<?=$key?>");
                let $btn_open_form = $container.find("[data-btn-add]");
                let $btn_save_form = $container.find("[data-btn-save]");
                let $btn_close_form = $container.find("[data-btn-close]");
                let $container_form = $container.find("[data-form-add]");
                let $comment = $container.find("[data-input-comment-add]");
                let $value = $container.find("[data-input-value]");

                $btn_close_form.click(function () {
                    $btn_open_form.show('fast');
                    $container_form.hide('fast');
                });

                $btn_open_form.click(function () {
                    $btn_open_form.hide('fast');
                    $container_form.show('fast');
                });

                $btn_save_form.click(function () {
                    let comment = $comment.val().trim();
                    if (comment.length == 0) {
                        PNotify.error("Введите комментарий");
                        return;
                    }
                    $value.removeAttr('disabled');
                    saveIdentifier(this, <?=$task->id?>, <?=$identifier->id?>, true);
                });
            });
        </script>
    <?php endif; ?>
</div>
