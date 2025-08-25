<?php

use app\modules\process\models\Req3Corrections;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\template_steps\Req3TemplateStepRuleFunctions;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $functionsBtn Req3TemplateStepRuleFunctions[] */
?>

<?php if (count($functionsBtn) > 0): ?>
    <div>
        <span style="color: #9f9f9f; font-size: small"><i class="fas fa-info-circle" style="color: #c3c3c3"></i> Ф-ции которые можно запустить вручную:</span>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php foreach ($functionsBtn as $function): ?>
                <div class="btn-group">
                    <?php if ($function->isLimitExceeded($task)): ?>
                        <button class="btn btn-outline-primary btn-sm shadow disabled" title="Достигнут лимит запуска">
                            <i class="fas fa-battery-full"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm shadow disabled" title="Достигнут лимит запуска">
                            <i class="fas fa-meteor"></i> <?= $function->btn_name ?>
                            <i class="fas fa-pen-fancy btn-correct left" style="color: #c6c6c6;" onclick="showDialogCorrect(event, <?= $function->id ?>, <?= Req3Corrections::LINK_TYPE_FUNCTION ?>)"></i>
                        </button>
                    <?php elseif ($task->isAccessAction(Yii::$app->user->identity)): ?>
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="showDialogStartFunction(<?= $task->id ?>, <?= $function->id ?>)">
                            <i class="fas fa-meteor"></i> <?= $function->btn_name ?>
                            <i class="fas fa-pen-fancy btn-correct left" style="color: #c6c6c6;" onclick="showDialogCorrect(event, <?= $function->id ?>, <?= Req3Corrections::LINK_TYPE_FUNCTION ?>)"></i>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-outline-primary btn-sm shadow disabled" title="У вас нет прав">
                            <i class="fas fa-user-lock"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm shadow disabled" title="У вас нет прав">
                            <i class="fas fa-meteor"></i> <?= $function->btn_name ?>
                            <i class="fas fa-pen-fancy btn-correct left" style="color: #c6c6c6;" onclick="showDialogCorrect(event, <?= $function->id ?>, <?= Req3Corrections::LINK_TYPE_FUNCTION ?>)"></i>
                        </button>
                    <?php endif; ?>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>