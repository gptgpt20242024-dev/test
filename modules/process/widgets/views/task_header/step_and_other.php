<?php

use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $nextStepSetting array */
/* @var $chatsCount int */

?>

<div style="display: flex; flex-wrap: wrap; gap: 10px">

    <div style="display: flex; flex-wrap: wrap; gap: 10px">
        <?= $this->render('step_and_other/current_step', ['task' => $task]) ?>

        <?= $this->render('step_and_other/current_transition', ['task' => $task, 'nextStepSetting' => $nextStepSetting]) ?>
    </div>

    <?= $this->render('step_and_other/active_chat', ['task' => $task]) ?>

    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-left: auto">
        <?= $this->render('step_and_other/hired_oper', ['task' => $task]) ?>

        <button type="button" class="btn btn-outline-warning btn-sm shadow-sm text-left"
                title="Проблемы в БП"
                data-toggle="tooltip"
                onclick="showDialogProblems(this, <?= $task->id ?>)">
            <i class="fas fa-exclamation-triangle"></i> Проблемы в БП
            <div style="font-size: 11px; color: #9f9f9f;">Решения проблем</div>
        </button>

        <button type="button" class="btn btn-outline-primary btn-sm shadow-sm text-left" title="Поиск решения" onclick="searchSolution(<?= $task->id ?>)">
            <i class="fas fa-search"></i> Поиск решения
            <br>
            <span style="font-size: 11px; color: #9f9f9f; display: flex; justify-content: space-between; align-items: center">
                Ответы от ИИ
                <?php if ($chatsCount > 0): ?>
                    <span>
                        <i class="fas fa-comments fa-sm text-success position-relative mr-1"></i><?= $chatsCount ?>
                    </span>
                <?php endif; ?>
            </span>
        </button>
    </div>

</div>

<?php if ($task->isDeviationStep()): ?>
    <?= $this->render('step_and_other/deviation_control', ['task' => $task]) ?>
<?php endif; ?>

<script>
    let solutionDialog = null;

    function searchSolution(taskId) {
        if (solutionDialog) {
            solutionDialog.open();
            return;
        }

        solutionDialog = BootstrapDialog.show({
            size: BootstrapDialog.SIZE_LARGE,
            type: BootstrapDialog.TYPE_PRIMARY,
            title: "Поиск проблем",
            autodestroy: false,
            onshow: function (dialog) {
                let $content = dialog.getModalBody();

                $.load2({
                    url: generateUrl("/knowledge_base/chat/search-solution"),
                    method: 'GET',
                    data: {
                        'task_id': taskId,
                    },
                    container: $content,
                    fragment: "[data-solution]",
                    fragmentContentOnly: false,
                    replaceContainer: true,
                });
            }
        });
    }
</script>