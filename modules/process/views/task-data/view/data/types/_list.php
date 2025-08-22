<?php

use app\assets\RippleCheckboxAsset;
use app\components\Str;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3Corrections;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */
/* @var $values Req3TasksDataItems[] */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$is_check_list = $identifier->getSettingByKey(Req3Identifiers::SETTING_IS_CHECKLIST, 0) == 1;
$select_all = $identifier->getSettingByKey(Req3Identifiers::SETTING_NEED_SELECT_ALL, $is_check_list ? 1 : 0) == 1;
$is_multi = $identifier->is_multi;

RippleCheckboxAsset::register($this);
?>

<?php if (!$is_check_list): ?>
    <?php if ($value->list_item): ?>
        <?= $value->list_item->value ?>
    <?php else: ?>
        list_item<?= $value->value_id ?>
    <?php endif; ?>
<?php else: ?>
    <?php $key = "i_" . $identifier->id; ?>
    <style>
        .cbx-label {
            -webkit-user-select: auto;
            user-select: auto;
        }
    </style>

    <?php ActiveForm::begin(['options' => ['id' => "form_{$identifier->id}"]]) ?>

    <input name="<?= Html::getInputName($value, '[' . $key . ']type') ?>" type="hidden" value="<?= $identifier->type ?>"/>
    <input name="<?= Html::getInputName($value, '[' . $key . ']identifier_id') ?>" type="hidden" value="<?= $identifier->id ?>"/>

    <?php
    /** @var Req3TasksDataItems[] $checked */
    $checked = [];
    $items = [];
    ?>
    <?php if ($identifier->list_group): ?>
        <?php
        $checked = ArrayHelper::map($values, 'value_id', 'value_id');
        unset($checked[null]);
        $input_type = $is_multi ? "checkbox" : "radio";
        ?>

        <div class="option-input">
            <?php $items = $identifier->list_group->getItemsSorted(); ?>
            <?php $biggerThan5 = count($items) > 5; ?>
            <?php foreach ($items as $item): ?>
                <?php $isChecked = in_array($item->id, $checked)?>
                <?php $check_id = "list_{$identifier->id}_{$item->id}"; ?>
                <?php if (!$is_editable && $biggerThan5 && !$isChecked) continue; ?>
                <label class="<?= !($is_editable && !$is_only_view && $can_edit) ? "disabled" : "" ?>" style="<?= !($is_editable && !$is_only_view && $can_edit) && $isChecked ? "color:black; opacity: 1;" : "" ?> position: relative; z-index: 5;">
                    <input id="<?= $check_id ?>" name="<?= Html::getInputName($value, "[{$key}]value_id") ?>[]" value="<?= $item->id ?>" type="<?= $input_type ?>" <?= $isChecked ? "checked" : "" ?> <?= !($is_editable && !$is_only_view && $can_edit) ? "disabled" : "" ?> />
                    <?= Str::toLink($item->value) ?>
                    <i class="fas fa-pen-fancy btn-correct" onclick="showDialogCorrect(event, <?= $item->id ?>, <?= Req3Corrections::LINK_TYPE_LIST_ITEM ?>)"></i>
                </label>

            <?php if ($is_editable && !$is_only_view && $can_edit): ?>
                <script>
                    $('#<?=$check_id?>').on('change', function (a1, a2, a3) {
                        let e = this;
                        let $container = $(e).closest("[data-identifier]");
                        if (!window.hasOwnProperty("checked_identifier")) {
                            window['checked_identifier'] = {};
                        }
                        if (!window['checked_identifier'].hasOwnProperty("i<?=$identifier->id?>")) {
                            window['checked_identifier']["i<?=$identifier->id?>"] = [];
                        }
                        window['checked_identifier']["i<?=$identifier->id?>"].push(e);

                        let timer_id = $container.data("timer_id");
                        clearTimeout(timer_id);
                        timer_id = setTimeout(function () {
                            saveIdentifier(e, <?=$task->id?>, <?=$identifier->id?>, false, function () {
                                while (window['checked_identifier']["i<?=$identifier->id?>"].length) {
                                    $(window['checked_identifier']["i<?=$identifier->id?>"].pop()).prop("checked", false);
                                }
                            }, function () {
                                while (window['checked_identifier']["i<?=$identifier->id?>"].length) {
                                    window['checked_identifier']["i<?=$identifier->id?>"].pop();
                                }
                            });
                        }, 500);
                        $container.data("timer_id", timer_id);

                        let items = $container.find("input:checked");
                        let count = items.length;
                        let count_all = <?=count($items)?>;
                        let is_required = <?=$is_required ? "true" : "false"?>;
                        let is_multi = <?=$is_multi ? "true" : "false"?>;
                        let select_all = <?=$select_all ? "true" : "false"?>;

                        if ((is_multi && count == count_all) || (count > 0 && (!is_required || !is_multi || !select_all))) {
                            $container.find("[data-alert-fill]").hide('fast');
                        } else {
                            $container.find("[data-alert-fill]").show('fast');
                        }
                    });
                </script>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php ActiveForm::end() ?>

    <div style="margin: 0 -8px !important; <?= (($is_multi && count($checked) == count($items)) || (count($checked) > 0 && (!$is_required || !$is_multi || !$select_all))) ? "display: none" : "" ?>" data-alert-fill="1">
        <?= $this->render('/task-data/view/data/alert_fill', [
            'task'       => $task,
            'identifier' => $identifier,

            'is_required'        => $is_required,
            'is_editable'        => $is_editable,
            'is_only_view'       => $is_only_view,
            'is_custom_editable' => true,
            'can_edit'           => $can_edit,
        ]); ?>
    </div>

<?php endif; ?>

