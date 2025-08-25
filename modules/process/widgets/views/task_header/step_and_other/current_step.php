<?php

use app\components\Date;
use app\components\Str;
use app\modules\process\models\Req3Corrections;
use app\modules\process\models\task\Req3Tasks;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
?>

<div class="btn-group">

    <?php if ($task->escalation > 0): ?>
        <button type="button" class="btn btn-danger btn-sm disabled" title="Эскалация №<?= $task->escalation ?>" data-toggle="tooltip" style="">
            <?php if ($task->escalation == 1): ?>
                <i class="fas fa-meh"></i>
            <?php elseif ($task->escalation == 2): ?>
                <i class="far fa-frown"></i>
            <?php elseif ($task->escalation == 3): ?>
                <i class="fas fa-poo-storm"></i>
            <?php else: ?>
                <i class="fas fa-poo"></i>
            <?php endif; ?>
            <?= $task->escalation ?>
        </button>
    <?php endif; ?>


    <div type="button" class="btn btn-outline-primary btn-sm p-0 shadow-sm disabled" title="текущий шаг" data-toggle="tooltip" style="text-align: left; user-select: auto; color: #004f95; font-weight: bold; min-width: 200px; border-color: #cbcbcb; background-color: #ffffff">
        <div class="px-2 py-1">
            <?= $task->step->name ?? "-" ?>
            <?php $minutes = $task->getStepTimeLeft(); ?>
            <div style="font-size: 11px; color: #515151; font-weight: normal">
                <?php if ($minutes != 0): ?>

                    <?php if ($minutes > 0): ?>
                        Осталось: <?= Date::minutesToText($minutes, 2, true) ?>
                    <?php else: ?>
                        Просрочено: <?= Date::minutesToText($minutes * -1, 2, true) ?>
                    <?php endif; ?>

                <?php else: ?>
                    Без сроков
                <?php endif; ?>
            </div>
        </div>
        <?= $this->render('/task/view/progress/time_small', ['task' => $task]) ?>

        <i class="fas fa-pen-fancy btn-correct" onclick="showDialogCorrect(event, <?= $task->step_id ?>, <?= Req3Corrections::LINK_TYPE_STEP ?>)"></i>
    </div>

    <button type="button" onclick="showDialogStepRulesInfo(<?= $task->step_id ?>, <?= $task->id ?>)" class="btn btn-outline-secondary btn-sm shadow-sm" title="Информация о условиях шага" data-toggle="tooltip" style="display: flex; align-items: center; border-color: #cbcbcb; padding: 0 12px !important">
        <i class="fas fa-info"></i>
    </button>

    <?php if (Yii::$app->user->canMulti(["business.admin", "business.delete", "business.edit"])): ?>
        <a href="<?= Url::toRoute(['/process/step/edit', 'id' => $task->step_id]) ?>" class="btn btn-outline-secondary btn-sm shadow-sm" title="Изменить шаг" data-toggle="tooltip" style="display: flex; align-items: center; border-color: #cbcbcb;">
            <i class="fas fa-pencil-alt"></i>
        </a>
    <?php endif; ?>

    <a href="<?= Url::toRoute(['/process/step/view', 'id' => $task->step_id]) ?>" class="btn btn-outline-secondary btn-sm shadow-sm" title="Просмотр шага" data-toggle="tooltip" style="display: flex; align-items: center; border-color: #cbcbcb;">
        <i class="fas fa-eye"></i>
    </a>

    <?php if ($task->step && !empty($task->step->work_rated_id) && $task->step->work_rated): ?>
        <?php
        if (!empty($task->step->work_rated->standard_manual_id)) {
            $clickFunction = "openLinkManual('" . Url::toRoute(['/manual/doc/view', 'id' => $task->step->work_rated->standard_manual_id]) . "');";
            $icon = "fas fa-file-word";
            $class = "badge-primary";
        } else {
            $clickFunction = "showDialogBlockText('Стандарт', '#work_rater_{$task->step->work_rated->id}_0_text')";
            $icon = "fas fa-quote-left";
            $class = "badge-info";
        }
        ?>
        <?php if (empty($task->step->work_rated->standard_manual_id)): ?>
            <div style="display: none" id="work_rater_<?= $task->step->work_rated->id ?>_0_text">
                <?= Str::toLink(nl2br($task->step->work_rated->standard_text)) ?>
            </div>
        <?php endif; ?>

        <a href="javascript:void (0);" onclick="<?= $clickFunction ?>" class="btn btn-outline-secondary btn-sm shadow-sm" title="Просмотр шага" data-toggle="tooltip" style="display: flex; gap: 5px; align-items: center; border-color: #cbcbcb;">
            <i class="<?= $icon ?>"></i> <span>Стандарт</span>
        </a>
    <?php endif; ?>

</div>