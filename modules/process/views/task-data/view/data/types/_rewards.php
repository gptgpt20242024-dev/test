<?php

use app\assets\RippleCheckboxAsset;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3TaskRewardWork;
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

$is_multi = $identifier->is_multi;
RippleCheckboxAsset::register($this);
?>

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


<?php /** @var Req3TasksDataItems[] $checked */
$checked = ArrayHelper::getColumn($values, 'value_id');
$input_type = $is_multi ? "checkbox" : "radio";
$items = Req3TaskRewardWork::find()->andWhere(['task_id' => $task->id, 'is_confirmed' => 0])->all();
?>

    <div class="option-input">
        <?php foreach ($items as $item): ?>
            <?php $check_id = "list_{$identifier->id}_{$item->id}"; ?>

            <label class="<?= !($is_editable && !$is_only_view && $can_edit) ? "disabled" : "" ?>" style="<?= !($is_editable && !$is_only_view && $can_edit) && in_array($item->id, $checked) ? "color:black; opacity: 1;" : "" ?>">
                <input style="margin-bottom: 2px;" id="<?= $check_id ?>" name="<?= Html::getInputName($value, "[{$key}]value_id") ?>[]" value="<?= $item->id ?>" type="<?= $input_type ?>" <?= in_array($item->id, $checked) ? "checked" : "" ?> <?= !($is_editable && !$is_only_view && $can_edit) ? "disabled" : "" ?> />
                <b><?= $item->work_rater->name ?? "-" ?></b> (<span style="color: #06b320"><?= $item->value ?></span><span style="color: #acacac">р.</span>)
                <div style="display: flex;">
                    <span><?= $item->oper->fio ?? "-" ?></span>
                    <span style="margin-left: auto; color: #5d5d5d"><?= $item->date_add ?></span>
                </div>
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
                });
            </script>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>

<?php ActiveForm::end() ?>