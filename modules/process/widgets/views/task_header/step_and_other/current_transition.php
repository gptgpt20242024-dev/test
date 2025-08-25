<?php

use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $nextStepSetting array */

?>

<?php if ($nextStepSetting['haveTransitions']): ?>
    <div class="btn-group">
        <button type="button" class="btn <?= $nextStepSetting['arrow_class'] ?> btn-sm disabled"><i class="fas fa-arrow-right"></i></button>

        <button type="button" class="btn <?= $nextStepSetting['class'] ?> <?= !$nextStepSetting['access'] ? "disabled" : "" ?> btn-sm shadow-sm text-left pr-3"
                style="min-width: 150px"
            <?php if ($nextStepSetting['access']): ?>
                onclick="showDialogNextStep(<?= $task->id ?>, <?= $nextStepSetting['rule2_id'] ?>)"
                data-next-step="1"
            <?php endif; ?>
                title="Двинуть задачу на следующий шаг"
                data-toggle="tooltip">

            <?php if ($nextStepSetting['icon']): ?>
                <i class="<?= $nextStepSetting['icon'] ?>"></i>
            <?php endif; ?>

            <?php if ($task->isLastStep()): ?>
                Восстановить
            <?php else: ?>
                Далее
            <?php endif; ?>

            <?php if ($nextStepSetting['error']): ?>
                <div style="color:<?= $nextStepSetting['error_color'] ?>; font-size: 11px;">
                    <?= $nextStepSetting['error'] ?>
                </div>
            <?php elseif ($nextStepSetting['hint']): ?>
                <div style="color:#e0e0e0; font-size: 11px;">
                    <?= $nextStepSetting['hint'] ?>
                </div>
            <?php endif; ?>

        </button>
    </div>
<?php endif; ?>